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
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
use InsaLan\InsaLanBundle\GlobalVars;

use InsaLan\UserBundle;
use InsaLan\UserBundle\Entity\PaymentDetails;

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
        $usr = $this->get('security.token_storage')->getToken()->getUser();

        $logger = $this->get('logger');
        $logger->notice("userlogin" . "::" . $usr->getUsername() . "::" . $_SERVER['REMOTE_ADDR']);
        if (!$this->userProfileCompleted($usr))
            return $this->redirect($this->generateUrl('insalan_user_default_index'));

        $registrables = $em->getRepository('InsaLanTournamentBundle:Registrable')->findThisYearRegistrables();
        // participants can be either a single player, a team or a manager
        $participants = $em->getRepository('InsaLanTournamentBundle:Participant')->findByUser($usr);

        $registered = array();

        foreach($participants as $p) {
            $registered[] = $p->getRegistrable();

            if ($p->getRegistrable() instanceof Entity\Bundle)
                foreach($p->getRegistrable()->getTournaments() as $t)
                    $registered[] = $t;
        }

        foreach($registrables as $r) {
            if ($r instanceof Entity\Bundle) {
                foreach($r->getTournaments() as $t) {
                    if (array_search($t, $registered) !== false) {
                        $registered[] = $r;
                        break;
                    }
                }
            }
        }

        foreach($registered as $r) {
            if (array_search($r, $registrables) !== false)
                unset($registrables[array_search($r, $registrables)]);
        }

        return array(
            'registrables' => $registrables,
            'participants' => $participants,
            'session' => $this->get('session'),
        );
    }


    /**
     * Allow the user to chose where he want to be placed at the event
     * @Route("/{tournament}/user/placement")
     * @Template()
     */
    public function placementAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.token_storage')->getToken()->getUser();

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

        // Get global variables
        $globalVars = array();
        $globalKeys = ['romanNumber'];
        $globalVars = $em->getRepository('InsaLanBundle:GlobalVars')->getGlobalVars($globalKeys);

        // Room structure
        $structure = $this->get('insalan.tournament.placement')->getStructure();

        // Getting unavailable placements for interface
        $unavailable = $em->getRepository("InsaLanTournamentBundle:Tournament")->getUnavailablePlacements($tournament);
        return array('structure' => $structure, 'tournament' => $tournament, 'participant' => $participant, 'unavailable' => $unavailable, 'globalVars' => $globalVars);
    }

    /**
     * Manage all steps for registering into a tournament
     * @Route("/{registrable}/user/enroll")
     */
    public function enrollAction(Request $request, Entity\Registrable $registrable) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.token_storage')->getToken()->getUser();

        if (!$this->userProfileCompleted($usr))
            return $this->redirect($this->generateUrl('insalan_user_default_index'));

            // Check if registrations have started
        if ($registrable->isOpenedInFuture()) {
          $this->get('session')->getFlashBag()->add('error', "Les inscriptions ne sont pas encore ouvertes pour ce tournoi.");
          return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        }

        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingRegistrable($usr, $registrable);

        if ($player === null && $registrable->isLocked() && !$registrable->checkLocked($this->get('session')->get($registrable->getId() . ".authToken", ''))) {
            $this->get('session')->getFlashBag()->add('error', "Ce tournois n'est accessible que sur invitation.");
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        }
        else if ($player === null)
            return $this->redirect($this->generateUrl('insalan_tournament_user_setplayer',array('registrable' => $registrable->getId())));
        else if (!$player->getGameValidated())
            return $this->redirect($this->generateUrl('insalan_tournament_user_validateplayer',array('registrable' => $registrable->getId())));
        else if ($registrable instanceof Entity\Tournament && $registrable->getParticipantType() === 'team' && $player->getTeamForTournament($registrable) === null)
            return $this->redirect($this->generateUrl('insalan_tournament_user_jointeam',array('tournament' => $registrable->getId())));
        else if (!$player->getPaymentDone())
            return $this->redirect($this->generateUrl('insalan_tournament_user_pay',array('registrable' => $registrable->getId())));
        else
            return $this->redirect($this->generateUrl('insalan_tournament_user_paydone',array('registrable' => $registrable->getId())));
    }

    /**
     * Manage all steps for registering into a LOCKED tournament
     * @Route("/{registrable}/user/enroll/{authToken}")
     */
    public function enrollLockedAction(Request $request, Entity\Registrable $registrable, $authToken) {
        // check provided token
        if (!$registrable->checkLocked($authToken)) {
            $this->get('session')->getFlashBag()->add('error', "Ce tournois n'est accessible que sur invitation.");
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        } else { // from here we are authentificated
            $this->get('session')->set($registrable->getId() . ".authToken", $authToken);
            $this->get('session')->getMetadataBag()->stampNew(0);
        }

        return $this->redirect($this->generateUrl('insalan_tournament_user_enroll', array('registrable' => $registrable->getId())));
    }

    /**
     * Create new player for this tournament from user account
     * @Route("/{registrable}/user/player/set")
     * @Template()
     */
    public function setPlayerAction(Request $request, Entity\Registrable $registrable) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.token_storage')->getToken()->getUser();

        $player = $em->getRepository('InsaLanTournamentBundle:Player')->findOneByUserAndPendingRegistrable($usr, $registrable);

        if (($res = $this->checkLoginPlatform($registrable)) !== null)
            return $res;

        if ($player === null) {
            // Check if registrations have started
            if ($registrable->isOpenedInFuture()) {
              $this->get('session')->getFlashBag()->add('error', "Les inscriptions ne sont pas encore ouvertes pour ce tournoi.");
              return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
            }

            // make sure we are allowed to register
            if ($registrable->isLocked() && !$registrable->checkLocked($this->get('session')->get($registrable->getId() . ".authToken", ''))) {
                $this->get('session')->getFlashBag()->add('error', "Ce tournois n'est accessible que sur invitation.");
                return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
            }

            $player = new Player();
            $player->setUser($usr);
            $player->setPendingRegistrable($registrable);
        }

        if ($registrable instanceof Entity\Tournament && $registrable->getLoginType() != UserBundle\Service\LoginPlatform::PLATFORM_OTHER) {
            $name = $this->get("insalan.user.login_platform")->getGameName($usr, $registrable->getLoginType());

            $player->setGameName($name);
            $em->persist($player);
            $em->flush();

            return $this->redirect($this->generateUrl('insalan_tournament_user_validateplayer', array('registrable' => $registrable->getId())));
        }
        else {
            return $this->usernameSet($em, $usr, $player, $request, $registrable);
        }
    }

    /**
     * Prompt the user to fill-in his LoginPlatform infos
     * @Route("/{tournament}/user/player/registerLoginPlatform/")
     * @Template("InsaLanTournamentBundle:User:redirectToApiLogin.html.twig")
     */
    public function redirectToApiLoginAction(Request $request, Entity\Tournament $tournament) {
        return array('tournament' => $tournament);
    }

    /**
     * Manage validation of player registration into a tournament
     * @Route("/{registrable}/user/player/validate")
     */
    public function validatePlayerAction(Request $request, Entity\Registrable $registrable) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.token_storage')->getToken()->getUser();

        $player = $em->getRepository('InsaLanTournamentBundle:Player')->findOneByUserAndPendingRegistrable($usr, $registrable);

        if ($player === null) {
            return $this->redirect($this->generateUrl('insalan_tournament_user_setplayer',array('registrable' => $registrable->getId())));
        } else {

            $player->setGameValidated(true);
            $this->finalizePlayerAfterValidation($player, $registrable);
            $em->persist($player);
            $em->flush();

            if ($registrable instanceof Entity\Tournament && $registrable->getParticipantType() === "team") {
                return $this->redirect(
                    $this->generateUrl('insalan_tournament_user_jointeam', array('tournament' => $registrable->getId()))
                );
            } else {
                return $this->redirect(
                    $this->generateUrl('insalan_tournament_user_pay', array('registrable' => $registrable->getId()))
                );
            }

        }
    }

    private function finalizePlayerAfterValidation(Entity\Player $player, Entity\Registrable $registrable) {
        if($registrable instanceof Entity\Tournament && $registrable->getParticipantType() === "player") {
            $player->setTournament($registrable);
        }
    }

    /**
     * Allow a player to drop a pending tournament registration if not managed by team
     * @Route("/{registrable}/user/leave")
     */
    public function leaveAction(Entity\Registrable $registrable) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingRegistrable($usr, $registrable);

        if($player->getTournament() !== null && $player->getTournament()->getParticipantType() !== "player")
            throw new ControllerException("Not Allowed"); // must be a player only tournament

        // not allowed if he paid something
        if(!$player->getRegistrable()->isFree() && $player->getPaymentDone()){
            $this->get('session')->getFlashBag()->add('error', "Vous avez payé votre place, merci de contacter l'InsaLan si vous souhaitez vous désister.");
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        }

        // not allowed either if registration are closed
        if(!$player->getRegistrable()->isOpenedNow())
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        $em->remove($player);
        $em->flush();

        return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
    }

    /**
     * Payement doing and details
     * @Route("/{registrable}/user/pay/details")
     * @Route("/{registrable}/user/discount/{discount}/details", requirements={"discount" = "\d+"})
     * @Template()
     */
    public function payAction(Entity\Registrable $registrable, $discount = null) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingRegistrable($usr, $registrable);

        $discount = $em->getRepository('InsaLanUserBundle:Discount')
                       ->findOneById($discount);

        $discounts = $em
            ->getRepository('InsaLanUserBundle:Discount')
            ->findByRegistrable($registrable);

        if ($discount !== null && $discount->getRegistrable()->getId() !== $registrable->getId()){
            return $this->redirect($this->generateUrl('insalan_tournament_user_pay', array("registrable" => $registrable->getId())));
        }

        if($registrable->isFree()) {
            $player->setPaymentDone(true);
            $em->persist($player);
            $em->flush();
            return $this->redirect($this->generateUrl('insalan_tournament_user_paydone', array("registrable" => $registrable->getId())));
        }

        return array('registrable' => $registrable, 'user' => $usr, 'player' => $player, 'discounts' => $discounts, 'selectedDiscount' => $discount);
    }

    /**
     * Paypal stuff
     * @Route("/{registrable}/user/pay/paypal_ec")
     * @Route("/{registrable}/user/discount/{discount}/paypal_ec", requirements={"discount" = "\d+"})
     */
    public function payPaypalECAction(Entity\Registrable $registrable, $discount = null) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingRegistrable($usr, $registrable);

        $discount = $em->getRepository('InsaLanUserBundle:Discount')
                       ->findOneById($discount);

        if ($discount !== null && $discount->getRegistrable()->getId() !== $registrable->getId()){
            return $this->redirect($this->generateUrl('insalan_tournament_user_pay', array("registrable" => $registrable->getId())));
        }

        $price = $registrable->getWebPrice();
        $title = 'Place pour le tournoi '.$registrable->getName();

        if ($discount !== null){
            $price -= $discount->getAmount();
            $title .= " (" . $discount->getName() . ")";
        }

        $payment = $this->get("insalan.user.payment");
        $order = $payment->getOrder($registrable->getCurrency(), $price + $registrable->getOnlineIncreaseInPrice());

        $order->setUser($usr);
        $order->setDiscount($discount);

        $order->setRawPrice($price);
        $order->setPlace(PaymentDetails::PLACE_WEB);
        $order->setType(PaymentDetails::TYPE_PAYPAL);

        $order->addPaymentDetail($title, $price, '');
        $order->addPaymentDetail('Majoration paiement en ligne', $registrable->getOnlineIncreaseInPrice(), 'Frais de gestion du paiement');

        return $this->redirect(
            $payment->getTargetUrl(
                $order,
                'insalan_tournament_user_paydonetemp',
                array('registrable' => $registrable->getId())
            )
        );
    }

    /**
     * Payment sum up
     * @Route("/{registrable}/user/pay/done")
     * @Template()
     */
    public function payDoneAction(Request $request, Entity\Registrable $registrable) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingRegistrable($usr, $registrable);

        // Get global variables
        $globalVars = array();
        $globalKeys = ['helpPhoneNumber'];
        $globalVars = $em->getRepository('InsaLanBundle:GlobalVars')->getGlobalVars($globalKeys);

        return array('registrable' => $registrable, 'user' => $usr, 'player' => $player, 'globalVars' => $globalVars);
    }

    /**
     * @Route("/{registrable}/user/pay/done_temp")
     */
    public function payDoneTempAction(Request $request, Entity\Registrable $registrable) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingRegistrable($usr, $registrable);

        $payment = $this->get("insalan.user.payment");

        if ($payment->check($request, true)) {
            $player->setPaymentDone(true);
            $em->persist($player);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('insalan_tournament_user_paydone', array('registrable' => $registrable->getId())));
    }

    /**
     * Offer offline payment choices
     * @Route("/{registrable}/user/pay/offline")
     * @Route("/{registrable}/user/discount/{discount}/offline", requirements={"discount" = "\d+"})
     * @Template()
     */
    public function payOfflineAction(Request $request, Entity\Registrable $registrable, $discount = null) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingRegistrable($usr, $registrable);

        $discount = $em->getRepository('InsaLanUserBundle:Discount')
                       ->findOneById($discount);
        // Get global variables
        $globalVars = array();
        $globalKeys = ['payCheckAddress'];
        $globalVars = $em->getRepository('InsaLanBundle:GlobalVars')->getGlobalVars($globalKeys);

        if ($discount !== null && $discount->getRegistrable()->getId() !== $registrable->getId()){
            return $this->redirect($this->generateUrl('insalan_tournament_user_pay', array("registrable" => $registrable->getId())));
        }

        return array('registrable' => $registrable, 'user' => $usr, 'player' => $player, 'discount' => $discount, 'globalVars' => $globalVars);
    }

    /**
     * Allow a user to join a team in a tournament
     * @Route("{tournament}/user/join/team")
     * @Template()
     */
    public function joinTeamAction(Entity\Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingRegistrable($usr, $tournament);

        // Check if there is a player associated to this user
        if ($player === null || !$player->isNamed($tournament->getType())) {
            return $this->redirect(
                $this->generateUrl(
                    'insalan_tournament_user_setplayer',
                    array(
                        'registrable' => $tournament->getId()
                    )));
        }
        // Check if this player is validated for the game
        else if (!$player->isValidated($tournament->getType())) {
            return $this->redirect(
                $this->generateUrl(
                    'insalan_tournament_user_validateplayer',
                    array(
                        'registrable' => $tournament->getId()
                    )));
        }

        return array('registrable' => $tournament, 'user' => $usr, 'player' => $player);
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
        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingRegistrable($usr, $team->getTournament());

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
     * Allow a captain to edit his team's name and password
     * @Route("/user/edit/team/{teamId}")
     * @Template()
     */

    public function editTeamAction(Request $request, $teamId) {
        $em = $this->getDoctrine()->getManager();

        $team = $em
            ->getRepository('InsaLanTournamentBundle:Team')
            ->findOneById($teamId);

        // does the team exist ?
        if($team === null)
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        // get current logged user corresponding player
        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $captain = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingRegistrable($usr, $team->getTournament());

        // is he really the captain ? (also check for null)
        if($team->getCaptain() !== $captain)
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        $form = $this->createForm('InsaLan\TournamentBundle\Form\TeamType',
                                  $team,
                                  array(
                                        'method' => 'POST',
                                        'action' => $this->generateUrl('insalan_tournament_user_editteam', array('teamId' => $teamId)),
                                        'attr' => array('id' => 'step1')
                                      ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            // update password if not empty
            if ($team->getPlainPassword() != ""){
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($usr);
                $team->setPassword($encoder->encodePassword($team->getPlainPassword(), $team->getPasswordSalt()));
            }

            $em->persist($team);
            $em->flush();

            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        }

        $tournament = $team->getTournament();

        return array('team' => $team, 'tournament' => $tournament, 'form' => $form->createView());
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
        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $captain = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingRegistrable($usr, $team->getTournament());

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
        if(!$team->getTournament()->isFree() && $team->getValidated() && $team->getPlayers()->count() <= $team->getTournament()->getTeamMinPlayer()) {
            $this->get('session')->getFlashBag()->add('error', "Votre équipe est validée et si vous bannissez ce joueur vous serez en dessous du nombre minimum de joueurs.");
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        }

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
        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $captain = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingRegistrable($usr, $team->getTournament());

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

        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingRegistrable($usr, $tournament);

        $team = new Team();

        $form = $this->createForm('InsaLan\TournamentBundle\Form\TeamType',
                                  $team,
                                  array(
                                        'method' => 'POST',
                                        'action' => $this->generateUrl('insalan_tournament_user_createteam', array('tournament' => $tournament->getId())),
                                        'attr' => array('id' => 'step4')
                                      ));
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
            return $this->redirect($this->generateUrl('insalan_tournament_user_pay', array('registrable' => $tournament->getId())));
        }

        return array('registrable' => $tournament, 'user' => $usr, 'player' => $player, 'form' => $form->createView());
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

        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingRegistrable($usr, $tournament);

        $team = new Team();

        $form = $this->createForm(TeamLoginType::class,
                                  $team,
                                  array(
                                        'method' => 'POST',
                                        'action' => $this->generateUrl('insalan_tournament_user_existingteam', array('tournament' => $tournament->getId())),
                                        'attr' => array('id' => 'step4')
                                      ));
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
                    return $this->redirect($this->generateUrl('insalan_tournament_user_pay', array('registrable' => $tournament->getId())));
                } else {
                    throw new ControllerException("Mot de passe invalide");
                }
            } catch (ControllerException $e) {
                $details = $e->getMessage();
            }

        }
        return array('registrable' => $tournament, 'user' => $usr, 'player' => $player, 'error' => $details, 'form' => $form->createView());
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
            ->add('replayFile', FileType::class, array("label" => "Fichier"))
            ->add('save', SubmitType::class, array("label" => "Ajouter le fichier"))
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

    /**
     * Check if the user needs to provide LoginPlatform infos for one of the tournaments.
     * Redirects if needed
     */
    protected function checkLoginPlatform(Entity\Registrable $registrable)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $this->get('session')->set('callbackRegisterApiRoute','insalan_tournament_user_setplayer');
        $this->get('session')->set('callbackRegisterApiParams',array('registrable' => $registrable->getId()));

        if ($registrable instanceof Entity\Bundle) {
            foreach($registrable->getTournaments() as $t) {
                if (($res = $this->checkLoginPlatform($t)) !== null)
                    return $res;
            }
        }
        else {
            if (($registrable->getLoginType() == UserBundle\Service\LoginPlatform::PLATFORM_STEAM && $user->getSteamId() == null)
                || ($registrable->getLoginType() == UserBundle\Service\LoginPlatform::PLATFORM_BATTLENET && $user->getBattleTag() == null))
                return $this->redirect($this->generateUrl('insalan_tournament_user_redirecttoapilogin', array('tournament' => $registrable->getId())));
        }

        return null;
    }

    protected function usernameSet($em, UserBundle\Entity\User $usr, Entity\Player $player, $request, Entity\Registrable $registrable) {
        $form = $this->createForm(SetPlayerName::class,
                                  $player,
                                  array(
                                        'method' => 'POST',
                                        'action' => $this->generateUrl('insalan_tournament_user_setplayer', array('registrable' => $registrable->getId())),
                                        'attr' => array('id' => 'step1')
                                      ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $player->setGameValidated(false);
            $em->persist($player);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('insalan_tournament_user_validateplayer', array('registrable' => $registrable->getId()))
            );
        }

        return array('form' => $form->createView(), 'registrable' => $registrable);
    }

    protected function userProfileCompleted($usr) {
        if ($usr->getFirstname() == null || $usr->getFirstname() == "" || $usr->getLastname() == null || $usr->getLastname() == "" || $usr->getPhoneNumber() == null || $usr->getPhoneNumber() == "" || $usr->getBirthdate() == null) {
            $this->get('session')->getFlashBag()->add(
                'info',
                'Il nous manque encore quelques informations...'
            );
            return false;
        }

        return true;
    }

    private function isUserInTeam(Entity\Participant $part) {

        $user = $this->get('security.token_storage')->getToken()->getUser();

        if($part instanceof Entity\Team) {

            foreach ($part->getPlayers() as $p) {
                if($p->getUser() !== null && $p->getUser()->getId() === $user->getId())
                    return true;
            }
            return false;
        }

        return $part->getUser() === $user && $user !== null;

    }

}
