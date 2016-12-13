<?php

/**
 * TODO
 *
 * Clean this ugly file, simplify it and remove qualifications own logic.
 *
 */

namespace InsaLan\TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Payum\Core\Model\Order;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHumanStatus;
use Payum\Offline\PaymentFactory as OfflinePaymentFactory;
use Payum\Paypal\ExpressCheckout\Nvp\Api;

use InsaLan\TournamentBundle\Form\SetPlayerName;
use InsaLan\TournamentBundle\Form\TeamType;
use InsaLan\TournamentBundle\Form\TeamLoginType;
use InsaLan\TournamentBundle\Exception\ControllerException;

use InsaLan\TournamentBundle\Entity\Player;
use InsaLan\TournamentBundle\Entity\Team;
use InsaLan\TournamentBundle\Entity;

/**
 * User tournament registering and management interface
 */
class UserController extends Controller
{
    /**
     * User's homepage indexing all registered and available tournaments
     * @Route("/user")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();

        if ($usr->getFirstname() == null || $usr->getFirstname() == "" || $usr->getLastname() == null || $usr->getLastname() == "" || $usr->getPhoneNumber() == null || $usr->getPhoneNumber() == "" || $usr->getBirthdate() == null) {
            $this->get('session')->getFlashBag()->add(
                'info',
                'Il nous manque encore quelques informations...'
            );
            return $this->redirect($this->generateUrl('insalan_user_default_index'));
        }

        $rawTournaments = $em->getRepository('InsaLanTournamentBundle:Tournament')->findThisYearTournaments();
        // participants can be either a single player, a team or a manager
        $participants = $em->getRepository('InsaLanTournamentBundle:Participant')->findByUser($usr);

        $tournaments = array();
        foreach($rawTournaments as $t) {
            $in = false;
            foreach($participants as $p) {
                if($p->getTournament()->getId() === $t->getId()) {
                    $in = true;
                    break;
                }
            }
            if(!$in)
                $tournaments[] = $t;
        }

        return array('tournaments' => $tournaments, 'participants' => $participants);
    }


    /**
     * Allow the user to chose where he want to be placed at the event
     * @Route("/{tournament}/user/placement")
     * @Template()
     */
    public function placementAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();

        if(!$tournament->isPending() || !$tournament->getPlacement()) {
            $this->get('session')->getFlashBag()->add('error', "Le tournoi ne permet pas de choisir de places actuellement.");
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        }

        $participant = $em->getRepository('InsaLanTournamentBundle:Participant')
                          ->findOneByUserAndTournament($usr, $tournament);

        if(!$participant ||
            $participant->getValidated() !== Entity\Participant::STATUS_VALIDATED ||
            $participant instanceof Entity\Team &&
            $participant->getCaptain()->getUser() !== $usr) {
            $this->get('session')->getFlashBag()->add('error', "Seul le capitaine peut choisir une place pour l'équipe.");
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        }

        if(null !== ($placement = $request->query->get("placement"))) {
            $placement = intval($placement);
            $registered = $em->getRepository('InsaLanTournamentBundle:Participant')
                             ->findOneBy(array("placement" => $placement, "tournament" => $tournament));

            if($registered === null || $registered === $participant) {
                $participant->setPlacement(intval($placement));
                $this->get('session')->getFlashBag()->add('info', "La place vous a bien été attribuée !");
                $em->persist($participant);
                $em->flush();
            } else {
                $this->get('session')->getFlashBag()->add('error', "Un autre participant occupe déjà cette place.");
            }
        }

        // Room structure
        $structure = $this->get('insalan.tournament.placement')->getStructure();

        // Getting unavailable placements for interface
        $unavailable = $em->getRepository("InsaLanTournamentBundle:Tournament")->getUnavailablePlacements($tournament);
        return array('structure' => $structure, 'tournament' => $tournament, 'participant' => $participant, 'unavailable' => $unavailable);
    }

    /**
     * Manage all steps for registering into a tournament
     * @Route("/{tournament}/user/enroll")
     */
    public function enrollAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.context')->getToken()->getUser();

        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        if ($player === null && $tournament->isLocked()) {
            $this->get('session')->getFlashBag()->add('error', "Ce tournois n'est accessible que sur invitation.");
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        }
        else if ($player === null)
            return $this->redirect($this->generateUrl('insalan_tournament_user_setplayer',array('tournament' => $tournament->getId())));
        else if (!$player->getGameValidated())
            return $this->redirect($this->generateUrl('insalan_tournament_user_validateplayer',array('tournament' => $tournament->getId())));
        else if ($tournament->getParticipantType() === 'team' && $player->getTeamForTournament($tournament) === null)
            return $this->redirect($this->generateUrl('insalan_tournament_user_jointeam',array('tournament' => $tournament->getId())));
        else if (!$player->getPaymentDone())
            return $this->redirect($this->generateUrl('insalan_tournament_user_pay',array('tournament' => $tournament->getId())));
        else
            return $this->redirect($this->generateUrl('insalan_tournament_user_paydone',array('tournament' => $tournament->getId())));
    }

    /**
     * Manage all steps for registering into a LOCKED tournament
     * @Route("/{tournament}/user/enroll/{authToken}")
     */
    public function enrollLockedAction(Request $request, Entity\Tournament $tournament, $authToken) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.context')->getToken()->getUser();

        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        // check provided token
        if ($player === null && !$tournament->checkLocked($authToken)) {
            $this->get('session')->getFlashBag()->add('error', "Ce tournois n'est accessible que sur invitation.");
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        } // from here we are authentificated
        else if ($player === null)
            return $this->redirect($this->generateUrl('insalan_tournament_user_setplayer',array('tournament' => $tournament->getId())));
        else if (!$player->getGameValidated())
            return $this->redirect($this->generateUrl('insalan_tournament_user_validateplayer',array('tournament' => $tournament->getId())));
        else if ($tournament->getParticipantType() === 'team' && $player->getTeamForTournament($tournament) === null)
            return $this->redirect($this->generateUrl('insalan_tournament_user_jointeam',array('tournament' => $tournament->getId())));
        else if (!$player->getPaymentDone())
            return $this->redirect($this->generateUrl('insalan_tournament_user_pay',array('tournament' => $tournament->getId())));
        else
            return $this->redirect($this->generateUrl('insalan_tournament_user_paydone',array('tournament' => $tournament->getId())));
    }

    /**
     * Create new player for this tournament from user account
     * @Route("/{tournament}/user/player/set")
     * @Template()
     */
    public function setPlayerAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em->getRepository('InsaLanTournamentBundle:Player')->findOneByUserAndPendingTournament($usr, $tournament);
        $game = $tournament->getType();

        if ($player === null) {
            $player = new Player();
            $player->setUser($usr);
            $player->setPendingTournament($tournament);
        }

        return $this->usernameSet($em, $usr, $player, $request, $tournament);
    }

    /**
     * Manage validation of player registration into a tournament
     * @Route("/{tournament}/user/player/validate")
     */
    public function validatePlayerAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em->getRepository('InsaLanTournamentBundle:Player')->findOneByUserAndPendingTournament($usr, $tournament);
        $game = $tournament->getType();
        $check = $request->query->get('check') === "yes";

        if ($player === null) {
            return $this->redirect($this->generateUrl('insalan_tournament_user_setplayer'));
        } else {

            $player->setGameValidated(true);
            $this->finalizePlayerAfterValidation($player, $tournament);
            $em->persist($player);
            $em->flush();

            if ($tournament->getParticipantType() === "team") {
                return $this->redirect(
                    $this->generateUrl('insalan_tournament_user_jointeam', array('tournament' => $tournament->getId()))
                );
            } else {
                return $this->redirect(
                    $this->generateUrl('insalan_tournament_user_pay', array('tournament' => $tournament->getId()))
                );
            }

        }
    }

    private function finalizePlayerAfterValidation($player, $tournament) {
        if($tournament->getParticipantType() === "player") {
            $player->setTournament($tournament);
        }
    }

    /**
     * Allow a player to drop a pending tournament registration if not managed by team
     * @Route("/{tournament}/user/leave")
     */
    public function leaveAction(Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        if($player->getTournament()->getParticipantType() !== "player")
            throw new ControllerException("Not Allowed"); // must be a player only tournament

        $em->remove($player);
        $em->flush();

        return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
    }

    /**
     * Payement doing and details
     * @Route("/{tournament}/user/pay/details")
     * @Template()
     */
    public function payAction(Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        if($tournament->isFree()) {
            $player->setPaymentDone(true);
            $em->persist($player);
            $em->flush();
            return $this->redirect($this->generateUrl('insalan_tournament_user_paydone', array("tournament" => $tournament->getId())));
        }

        return array('tournament' => $tournament, 'user' => $usr, 'player' => $player);
    }

    /**
     * Paypal stuff
     * @Route("/{tournament}/user/pay/paypal_ec")
     */
    public function payPaypalECAction(Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        $paymentName = 'paypal_express_checkout_and_doctrine_orm';

        $price = ($tournament->getWebPrice() + $tournament->getOnlineIncreaseInPrice());

        $storage =  $this->get('payum')->getStorage('InsaLan\UserBundle\Entity\PaymentDetails');
        $order = $storage->createModel();
        $order->setUser($usr);

        $order['PAYMENTREQUEST_0_CURRENCYCODE'] = $tournament->getCurrency();
        $order['PAYMENTREQUEST_0_AMT'] = $price;

        $order['L_PAYMENTREQUEST_0_NAME0'] = 'Place pour le tournoi '.$tournament->getName();
        $order['L_PAYMENTREQUEST_0_AMT0'] = $tournament->getWebPrice();
        $order['L_PAYMENTREQUEST_0_DESC0'] = $tournament->getDescription();
        $order['L_PAYMENTREQUEST_0_NUMBER0'] = 1;

        $order['L_PAYMENTREQUEST_0_NAME1'] = 'Majoration paiement en ligne';
        $order['L_PAYMENTREQUEST_0_AMT1'] = $tournament->getOnlineIncreaseInPrice();
        $order['L_PAYMENTREQUEST_0_DESC1'] = 'Frais de gestion du paiement';
        $order['L_PAYMENTREQUEST_0_NUMBER1'] = 1;

        $storage->updateModel($order);

        $payment = $this->get('payum')->getPayment('paypal_express_checkout_and_doctrine_orm');
        $captureToken = $this->get('payum.security.token_factory')->createCaptureToken(
            $paymentName,
            $order,
            'insalan_tournament_user_paydonetemp',
            array('tournament' => $tournament->getId())
        );

        $order['RETURNURL'] = $captureToken->getTargetUrl();
        $order['CANCELURL'] = $captureToken->getTargetUrl();
        $order['INVNUM'] = $usr->getId();
        $storage->updateModel($order);
        return $this->redirect($captureToken->getTargetUrl());
    }

    /**
     * Payment sum up
     * @Route("/{tournament}/user/pay/done")
     * @Template()
     */
    public function payDoneAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        return array('tournament' => $tournament, 'user' => $usr, 'player' => $player);
    }

    /**
     * @Route("/{tournament}/user/pay/done_temp")
     */
    public function payDoneTempAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingTournament($usr, $tournament);


        $token = $this->get('payum.security.http_request_verifier')->verify($request);
        $payment = $this->get('payum')->getPayment($token->getPaymentName());

        //$this->get('payum.security.http_request_verifier')->invalidate($token);

        $payment->execute($status = new GetHumanStatus($token));


        if ($status->isCaptured()) {
            $player->setPaymentDone(true);
            $em->persist($player);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('insalan_tournament_user_paydone', array('tournament' => $tournament->getId())));
    }

    /**
     * Offer offline payment choices
     * @Route("/{tournament}/user/pay/offline")
     * @Template()
     */
    public function payOfflineAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        return array('tournament' => $tournament, 'user' => $usr, 'player' => $player);
    }

    /**
     * Allow a user to join a team in a tournament
     * @Route("{tournament}/user/join/team")
     * @Template()
     */
    public function joinTeamAction(Entity\Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        // Check if there is a player associated to this user
        if ($player === null || !$player->isNamed($tournament->getType())) {
            return $this->redirect(
                $this->generateUrl(
                    'insalan_tournament_user_setplayer',
                    array(
                        'tournament' => $tournament->getId()
                    )));
        }
        // Check if this player is validated for the game
        else if (!$player->isValidated($tournament->getType())) {
            return $this->redirect(
                $this->generateUrl(
                    'insalan_tournament_user_validateplayer',
                    array(
                        'tournament' => $tournament->getId()
                    )));
        }

        return array('tournament' => $tournament, 'user' => $usr, 'player' => $player);
    }

    /**
     * Allow a player to drop a pending tournament registration managed by teams
     * The user's registration to this tournament is cancelled !
     * @Route("/user/leave/team/{teamId}")
     * @Template()
     */
    public function leaveTeamAction($teamId) {
        $em = $this->getDoctrine()->getManager();
        $team = $em
            ->getRepository('InsaLanTournamentBundle:Team')
            ->findOneById($teamId);

        if($team === null)
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        // get targeted player
        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingTournament($usr, $team->getTournament());

        // is he part of the team roster ?
        if(!$team->haveInPlayers($player))
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        // not allowed if he paid something
        if(!$team->getTournament()->isFree() && $player->getPaymentDone()){
            $this->get('session')->getFlashBag()->add('error', "Vous avez payé votre place, merci de contacter l'InsaLan si vous souhaitez vous désister.");
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        }
        // not allowed either if registration are closed
        if(!$team->getTournament()->isOpenedNow())
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        $player->leaveTeam($team);
        $team->removePlayer($player);

        // if he was captain and not the last member, we need to chose another one
        if($team->getCaptain() === $player && $team->getPlayers()->count() === 0)
            $team->setCaptain($team->getPlayers()->first());

        // team cannot stay validated if someone leave and there is not enought players
        // TODO: Should be handled by the model ?
        if($team->getPlayers()->count() < $team->getTournament()->getTeamMinPlayer())
            $team->setValidated(Entity\Participant::STATUS_PENDING); // reset to waiting state

        $em->persist($team);

        if($team->getPlayers()->count() === 0)
            $em->remove($team);

        $em->remove($player);
        $em->flush();
        return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
    }

    /**
     * Allow a captain to ban another player from a team
     * Only possible if the player didn't pay anything yet OR if this is a free tournament
     * @Route("/user/ban/team/{teamId}/{playerId}")
     */
    public function banPlayerAction($teamId, $playerId) {
        $em =$this->getDoctrine()->getManager();
        $team = $em
            ->getRepository('InsaLanTournamentBundle:Team')
            ->findOneById($teamId);

        // does the team exist ?
        if($team === null)
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        // not allowed if registration are closed
        if(!$team->getTournament()->isOpenedNow())
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        // get current logged user corresponding player
        $usr = $this->get('security.context')->getToken()->getUser();
        $captain = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingTournament($usr, $team->getTournament());

        // is he really the captain ? (also check for null)
        if($team->getCaptain() !== $captain)
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        $playerToBan = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneById($playerId);

        // does this player exist ?
        if($playerToBan === null)
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        // not possible if the player payed something ! (paid tournament + payement done)
        if(!$team->getTournament()->isFree() && $playerToBan->getPaymentDone())
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        /**
         * Manage team validation state
         * A the moment :
         * - A validated team roster cannot be modified in paid tournaments
         * - Bans invalidates the team (see below)
         */
        if(!$team->getTournament()->isFree() && $team->getValidated())
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        // captain cannot ban himself !
        if($playerToBan !== $captain) {

            $playerToBan->leaveTeam($team);
            $team->removePlayer($playerToBan);

            if($team->getValidated() === Entity\Participant::STATUS_WAITING
            || $team->getValidated() === Entity\Participant::STATUS_VALIDATED)
                $team->setValidated(Entity\Participant::STATUS_PENDING); // reset to waiting state

            $em->persist($team);

            $em->remove($playerToBan);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
    }

    /**
     * Change team captain
     * @Route("/user/promote/{teamId}/{playerId}")
     */
    public function promoteCaptainAction($teamId, $playerId){
        $em =$this->getDoctrine()->getManager();
        $team = $em
            ->getRepository('InsaLanTournamentBundle:Team')
            ->findOneById($teamId);

        // does the team exist ?
        if($team === null)
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        // get current logged user corresponding player
        $usr = $this->get('security.context')->getToken()->getUser();
        $captain = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingTournament($usr, $team->getTournament());

        // is he really the captain ? (also check for null)
        if($team->getCaptain() !== $captain)
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        $playerToPromote = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneById($playerId);

        // does this player exist ?
        if($playerToPromote === null)
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        // is he part of the team roster ?
        if(!$team->haveInPlayers($playerToPromote))
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        $team->setCaptain($playerToPromote);
        $em->persist($team);
        $em->flush();

        return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
    }

    /**
     * Create a team when joining a tournament
     * @Route("{tournament}/user/join/team/create")
     * @Template()
     */
    public function createTeamAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();

        if($tournament->getParticipantType() !== "team")
            throw new ControllerException("Not Allowed");

        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        $team = new Team();

        $form = $this->createForm(new TeamType(), $team);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($usr);
            $team->generatePasswordSalt();
            $team->setPassword($encoder->encodePassword($team->getPlainPassword(), $team->getPasswordSalt()));
            $team->setTournament($tournament);
            $player->joinTeam($team);
            $team->addPlayer($player);
            $em->persist($team);
            $em->persist($player);
            $em->flush();
            return $this->redirect($this->generateUrl('insalan_tournament_user_pay', array('tournament' => $tournament->getId())));
        }

        return array('tournament' => $tournament, 'user' => $usr, 'player' => $player, 'form' => $form->createView());
    }

    /**
     * Allow a player to join an existing team with credencials
     * @Route("{tournament}/user/join/team/existing")
     * @Template()
     */
    public function existingTeamAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();

        if($tournament->getParticipantType() !== "team")
            throw new ControllerException("Équipes non acceptées dans ce tournois");

        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        $team = new Team();

        $form = $this->createForm(new TeamLoginType(), $team);
        $form->handleRequest($request);

        $details = null;
        if ($form->isValid()) {
            try {
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($usr);
                $team2 = $em
                    ->getRepository('InsaLanTournamentBundle:Team')
                    ->findOneByNameAndTournament($team->getName(), $tournament);

                if($team2 === null || $team2->getTournament()->getId() !== $tournament->getId())
                    throw new ControllerException("Équipe invalide");

                $team->setPassword($encoder->encodePassword($team->getPlainPassword(), $team2->getPasswordSalt()));

                if ($team2->getPassword() === $team->getPassword()) {
                    $player->joinTeam($team2);
                    $team2->addPlayer($player);
                    $em->persist($player);
                    $em->persist($team2);
                    $em->flush();
                    return $this->redirect($this->generateUrl('insalan_tournament_user_pay', array('tournament' => $tournament->getId())));
                } else {
                    throw new ControllerException("Mot de passe invalide");
                }
            } catch (ControllerException $e) {
                $details = $e->getMessage();
            }

        }
        return array('tournament' => $tournament, 'user' => $usr, 'player' => $player, 'error' => $details, 'form' => $form->createView());
    }


    /**
     * Automated match validation using Riot API
     * TODO: Unsupported at the moment
     * @Route("/user/public/team/{id}/validate/{match}", requirements={"id" = "\d+"})
     * @Template()
     */
    public function teamValidateMatchAction(Entity\Participant $team, Entity\Match $match)
    {

        throw new ControllerException("Not supported");

        try {
            $pvpService = $this->get('insalan.tournament.pvp_net');

            if($match->getPart1() !== $team && $match->getPart2() !== $team)
                throw new ControllerException("Invalid team");

            if(!$this->isUserInTeam($team))
                throw new ControllerException("Invalid user");

            if($match->getState() != Entity\Match::STATE_ONGOING)
                throw new ControllerException("Invalid match: not in ongoing state");

            try {
                $matchResult = $pvpService->getGameResult($match->getPart1(), $match->getPart2());
                $data = $matchResult[1];
                $matchResult = $matchResult[0];
            } catch (\Exception $e) {
                throw new ControllerException($e->getMessage());
            }
        }
        catch (ControllerException $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
            return $this->redirect($this->generateUrl('insalan_tournament_user_teamdetails', array('id' => $team->getId())));
        }

        $round = new Entity\Round();
        $round->setMatch($match);

        $round->setScore1(0);
        $round->setScore2(0);

        $round->setData($data);

        if($matchResult) {
            $round->setScore1(1);
        }
        else {
            $round->setScore2(1);
        }

        // TODO : not for LoL only

        $match->setState(Entity\Match::STATE_FINISHED);

        $em = $this->getDoctrine()->getManager();
        $em->persist($round);
        $em->persist($match);

        $em->flush();

        if($match->getKoMatch()) {
            $em->getRepository("InsaLanTournamentBundle:KnockoutMatch")->propagateVictory($match->getKoMatch());
            $em->flush();
        }

        return $this->redirect($this->generateUrl('insalan_tournament_user_teamdetails', array('id' => $team->getId())));

    }

    /**
     * Add a replay to a round of the tournament
     * The replay is an uploaded file
     * @Route("/user/team/{id}/addReplay/{round}", requirements={"id" = "\d+"})
     * @Template()
     */
    public function roundAddReplayAction(Request $request, Entity\Participant $team, Entity\Round $round)
    {
        try {
            // Check security
            if(!$this->isUserInTeam($team))
                throw new ControllerException("Invalid user");

            if($round->getMatch()->getPart1()->getId() !== $team->getId()
                && $round->getMatch()->getPart2()->getId() !== $team->getId())
                throw new ControllerException("Invalid round");

            if($round->getReplay() !== null)
                throw new ControllerException("Le fichier a déjà été envoyé !");
        } catch (ControllerException $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
            return $this->redirect($this->generateUrl('insalan_tournament_user_teamdetails', array('id' => $team->getId())));
        }

        $form = $this->createFormBuilder($round)
            ->add('replayFile', 'file', array("label" => "Fichier"))
            ->add('save', 'submit', array("label" => "Ajouter le fichier"))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($round);
            $em->flush();

            return $this->redirect($this->generateUrl('insalan_tournament_user_teamdetails', array('id' => $team->getId())));
        }

        return array("form" => $form->createView());
    }

    /** PRIVATE **/

    protected function usernameSet($em, $usr, $player, $request, $tournament) {
        $form = $this->createForm(new SetPlayerName(), $player);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $player->setGameValidated(false);
            $em->persist($player);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('insalan_tournament_user_validateplayer', array('tournament' => $tournament->getId()))
            );
        }

        return array('form' => $form->createView(), 'selectedGame' => $tournament->getType(), 'tournamentId' => $tournament->getId());
    }

    protected function lolValidation($em, $usr, $player, $tournamentId, $check) {
        if ($player->getGameValidated()) {
            return $this->redirect(
                $this->generateUrl(
                    'insalan_tournament_user_jointeam',
                    array(
                        'id' => $tournamentId
                    )));
        } else if (!$check) {
            return array('player' => $player, 'error' => null, 'selectedGame' => 'lol', 'tournamentId' => $tournamentId);
        } else {
            $details = null;
            try {
                $this->fetchInfo($usr, $player);
                $em->persist($player);
                $em->flush();
            } catch(\Exception $e) {
                $className = get_class($e);

                if ('GuzzleHttp\\Exception\\ClientException' === $className && 404 == $e->getResponse()->getStatusCode()) {
                    $details = 'Invocateur introuvable sur EUW';
                }
                else if (0 === strpos($className, 'GuzzleHttp')) {
                    $details = 'Erreur de l\'API. Veuillez réessayer.';
                } else {
                    $details = 'Une erreur inconnue est survenue';
                }
            }
            return array('player' => $player, 'error' => $details, 'selectedGame' => 'lol', 'tournamentId' => $tournamentId);
        }

    }

    private function isUserInTeam(Entity\Participant $part) {

        $user = $this->get('security.context')->getToken()->getUser();

        if($part instanceof Entity\Team) {

            foreach ($part->getPlayers() as $p) {
                if($p->getUser() !== null && $p->getUser()->getId() === $user->getId())
                    return true;
            }
            return false;
        }

        return $part->getUser() === $user && $user !== null;

    }

    /**
     * LoL API : fetch player info to check the masteries pages for requirements
     * @param  User $user   targeted user
     * @param  Player $player player associated with user for the tournament to check
     */
    protected function fetchInfo($user, $player) {
        $apiLol = $this->container->get('insalan.lol');
        $apiSummoner = $apiLol->getApi()->summoner();
        $rSummoner = $apiSummoner->info($player->getGameName());
        $player->setGameId($rSummoner->id);
        $player->setGameName($rSummoner->name);
        $player->setGameAvatar($rSummoner->profileIconId);
        $masteryPages = $apiSummoner->masteryPages($player->getGameId());
        foreach ($masteryPages as $page) {
            if ($page->get('name') == 'insalan'.$user->getId()) {
                $player->setGameValidated(true);
                return;
            }
        }
        throw $this->createNotFoundException('La page de maîtrise n\'existe pas');
    }

    /**
     * LoL API : tournament code provider
     * Fills the provided match pvpNetURL with data provided
     * @param  Entity\Match $m Targeted match
     */
    private function populateTournamentCode(Entity\Match $m)
    {
        $pvpService = $this->get('insalan.tournament.pvp_net');
        $round = 1;
        $name = "InsaLan Match " . $m->getId() ." G".$round;
        $m->pvpNetUrl = $pvpService->generateUrl(array(
            "name" => $name,
            "extra" => $m->getId(),
            "pass" => md5('insalan_match_#'.$m->getId().'_'.$round)));
    }
}
