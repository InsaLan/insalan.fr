<?php

/**
 * ManagerController
 * All the stuff related to managers
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

use Payum\Core\Model\PizzaOrder;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHumanStatus;
use Payum\Offline\PaymentFactory as OfflinePaymentFactory;
use Payum\Paypal\ExpressCheckout\Nvp\Api;

use App\Form\SetManagerName;
use App\Form\SetPlayerName;
use App\Form\TeamLoginType;
use App\Exception\ControllerException;

use App\Entity\TournamentManager;
use App\Entity\Participant;
use App\Entity\Player;
use App\Entity\Tournament;
use App\Entity\tournamentTeam;
use App\Entity\User;
use App\Entity;

use App\Entity\PaymentDetails;

/**
* ManagerController
* All the stuff realted to managers management
* @Route("/manager")
*/
class TournamentManagerController extends Controller
{

    /**
     * Manage all steps for registering into a tournament as manager
     * @Route("/{tournament}/user/enroll")
     */
    public function enrollAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.token_storage')->getToken()->getUser();

        $manager = $em
            ->getRepository('App\Entity\TournamentManager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        if ($manager === null)
            return $this->redirect($this->generateUrl('app_tournamentmanager_setname',array('tournament' => $tournament->getId())));
        else if ($tournament->getParticipantType() === 'team' && $manager->getParticipant() === null)
            return $this->redirect($this->generateUrl('app_tournamentmanager_jointeamwithpassword',array('tournament' => $tournament->getId())));
        else if (!$manager->getPaymentDone())
            return $this->redirect($this->generateUrl('app_tournamentmanager_pay',array('tournament' => $tournament->getId())));
        else
            return $this->redirect($this->generateUrl('app_tournamentmanager_paydone',array('tournament' => $tournament->getId())));
    }

    /**
     * Create a new manager related to a tournament
     * @Route("/{tournament}/user/set")
     * @Template()
     */
    public function setNameAction(Request $request, Entity\Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.token_storage')->getToken()->getUser();

        $manager = $em->getRepository('App\Entity\TournamentManager')->findOneByUserAndPendingTournament($usr, $tournament);

        // TODO Map managers like players into the tournament entity ? This will make counting easier
        $countManagers = count($em->getRepository('App\Entity\TournamentManager')->findByTournament($tournament));
        if($tournament->getMaxManager() != null && $countManagers >= $tournament->getMaxManager()) {
            $this->get('session')->getFlashBag()->add('error', "Il ne reste plus de places manager sur ce tournois !");
            return $this->redirect($this->generateUrl('app_user_index'));
        }

        if ($manager === null) {
            $manager = new Manager();
            $manager->setUser($usr);
            $manager->setTournament($tournament);
        }

        $form = $this->createForm(SetManagerName::class,
                                  $manager,
                                  array(
                                        'method' => 'POST',
                                        'action' => $this->generateUrl('app_tournamentmanager_setname', array('tournament' => $tournament->getId())),
                                        'attr' => array('id' => 'step1')
                                      ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($manager);
            $em->flush();

            if($tournament->getParticipantType() === "team")
                return $this->redirect(
                    $this->generateUrl('app_tournamentmanager_jointeamwithpassword', array('tournament' => $tournament->getId()))
                );
            else // solo tournaments
                return $this->redirect(
                    $this->generateUrl('app_tournamentmanager_joinsoloplayer', array('tournament' => $tournament->getId()))
                );
        }

        return array('form' => $form->createView(), 'selectedGame' => $tournament->getType(), 'tournamentId' => $tournament->getId());
    }

    /**
     * Allow a manager to join a player in a solo tournament
     * @Route("/{tournament}/user/joinplayer")
     * @Template()
     */
    public function joinSoloPlayerAction(Request $request, Entity\Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.token_storage')->getToken()->getUser();

        // handle only solo tournaments
        if($tournament->getParticipantType() !== "player")
            throw new ControllerException("Joueurs solo non acceptées dans ce tournois");

        // check if there is already a pending manager for this user and tournament
        $manager = $em->getRepository('App\Entity\TournamentManager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        if($manager === null)
            return $this->redirect($this->generateUrl('app_tournamentmanager_setname', array('tournament' => $tournament->getId())));

        $form_player = new Player();
        $form = $this->createForm(SetPlayerName::class,
                                  $form_player,
                                  array(
                                        'method' => 'POST',
                                        'action' => $this->generateUrl('app_tournamentmanager_joinsoloplayer', array('tournament' => $tournament->getId())),
                                        'attr' => array('id' => 'step3')
                                      )); // fill player gamename
        $form->handleRequest($request);

        $error_details = null;
        if ($form->isValid()) {
            try {
                // find the targeted player related to the manager
                $player = $em
                    ->getRepository('App\Entity\Player')
                    ->findOneBy(array(
                        'gameName' => $form_player->getGameName(),
                        'tournament' => $tournament));

                if ($player === null)
                    throw new ControllerException("Joueur introuvable !");
                if ($player->getManager() != null)
                    throw new ControllerException("Le joueur possède déjà un manager !");

                $manager->setParticipant($player);
                $player->setManager($manager);
                $em->persist($manager);
                $em->persist($player);
                $em->flush();

                return $this->redirect($this->generateUrl('app_tournamentmanager_pay', array('tournament' => $tournament->getId())));

            } catch (ControllerException $e) {
                $error_details = $e->getMessage();
            }

        }
        return array('tournament' => $tournament, 'user' => $usr, 'manager' => $manager, 'error' => $error_details, 'form' => $form->createView());
    }

    /**
     * Allow a new manager to join a team with name and password
     * @Route("/{tournament}/user/jointeam")
     * @Template()
     */
    public function joinTeamWithPasswordAction(Request $request, Entity\Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.token_storage')->getToken()->getUser();

        // handle only team tournaments
        if($tournament->getParticipantType() !== "team")
            throw new ControllerException("Équipes non acceptées dans ce tournois");

        // check if there is already a pending manager for this user and tournament
        $manager = $em->getRepository('App\Entity\TournamentManager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        if($manager === null)
            return $this->redirect($this->generateUrl('app_tournamentmanager_setname', array('tournament' => $tournament->getId())));

        $form_team = new Team();
        $form = $this->createForm(TeamLoginType::class,
                                  $form_team,
                                  array(
                                        'method' => 'POST',
                                        'action' => $this->generateUrl('app_tournamentmanager_jointeamwithpassword', array('tournament' => $tournament->getId())),
                                        'attr' => array('id' => 'step4')
                                      )); // fill name and plainPassword
        $form->handleRequest($request);

        // inspired by UserController::joinExistingTeam
        // TODO rework this by putting the password hash into the request ?
        $error_details = null;
        if ($form->isValid()) {
            try {
                // hash password
                $defaultEncoder = new MessageDigestPasswordEncoder('sha512');

                $encoders = [
                    User::class => $defaultEncoder, // Your user class. This line specify you ant sha512 encoder for this user class
                ];

                $factory = new EncoderFactory($encoders);
                $encoder = $factory->getEncoder($usr);
                $team = $em
                    ->getRepository('App\Entity\TournamentTeam')
                    ->findOneByNameAndTournament($form_team->getName(), $tournament);

                if ($team === null || $team->getTournament()->getId() !== $tournament->getId())
                    throw new ControllerException("Équipe invalide");

                $form_team->setPassword($encoder->encodePassword($form_team->getPlainPassword(), $team->getPasswordSalt()));

                if ($team->getPassword() === $form_team->getPassword()) {
                    // denied if there is already a manager in the team
                    if ($team->getManager() != null)
                        throw new ControllerException("L'équipe a déjà un manager");

                    // Because PHP don't support polymorphism, we must get the corresponding Participant object
                    $team_participant = $em
                        ->getRepository('App\Entity\Participant')->
                        findOneById($team->getId());
                    $manager->setParticipant($team_participant);
                    $team->setManager($manager);
                    $em->persist($manager);
                    $em->persist($team);
                    $em->flush();

                    return $this->redirect($this->generateUrl('app_tournamentmanager_pay', array('tournament' => $tournament->getId())));

                } else
                    throw new ControllerException("Mot de passe invalide");
            } catch (ControllerException $e) {
                $error_details = $e->getMessage();
            }

        }
        return array('tournament' => $tournament, 'user' => $usr, 'manager' => $manager, 'error' => $error_details, 'form' => $form->createView());
    }

    /**
     * Allow a new manager to join a team with a specific token
     * @Route("/team/{team}/enroll/{authToken}")
     */
    public function joinTeamWithToken()
    {
        # code ...
    }

    /**
     * Payement doing and details for managers
     * @Route("/{tournament}/user/pay/details")
     * @Template()
     */
    public function payAction(Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $manager = $em
            ->getRepository('App\Entity\TournamentManager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        if($tournament->isFree()) {
            $manager->setPaymentDone(true);
            $em->persist($manager);
            $em->flush();
            return $this->redirect($this->generateUrl('app_tournamentmanager_paydone', array("tournament" => $tournament->getId())));
        }

        return array('tournament' => $tournament, 'user' => $usr, 'manager' => $manager);
    }

    /**
     * Paypal stuff
     * @Route("/{tournament}/user/pay/paypal_ec")
     */
    public function payPaypalECAction(Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $manager = $em
            ->getRepository('App\Entity\TournamentManager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        $price = ($manager::ONLINE_PRICE + $tournament->getOnlineIncreaseInPrice());

        $payment = $this->get("insalan.user.payment");
        $order = $payment->getOrder($tournament->getCurrency(), $price);

        $order->setUser($usr);

        $order->setRawPrice($manager::ONLINE_PRICE);
        $order->setPlace(PaymentDetails::PLACE_WEB);
        $order->setType(PaymentDetails::TYPE_PAYPAL);

        $order->addPaymentDetail('Place manager pour le tournoi '.$tournament->getName(), $manager::ONLINE_PRICE, '');
        $order->addPaymentDetail('Majoration paiement en ligne', $tournament->getOnlineIncreaseInPrice(), 'Frais de gestion du paiement');

        return $this->redirect(
            $payment->getTargetUrl(
                $order,
                '_tournamentmanager_paydonetemp',
                array('tournament' => $tournament->getId())
            )
        );
    }

    /**
     * Payment sum up
     * @Route("/{tournament}/user/pay/done")
     * @Template()
     */
    public function payDoneAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $manager = $em
            ->getRepository('App\Entity\TournamentManager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        // Get global variables
        $globalVars = array();
        $globalKeys = ['helpPhoneNumber'];
        $globalVars = $em->getRepository('App\Entity\InsaLanGlobalVars')->getGlobalVars($globalKeys);

        return array('tournament' => $tournament, 'user' => $usr, 'manager' => $manager, 'globalVars' => $globalVars);
    }

    /**
     * Perform payment validation
     * @Route("/{tournament}/user/pay/done_temp")
     */
    public function payDoneTempAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $player = $em
            ->getRepository('App\Entity\TournamentManager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        $payment = $this->get("insalan.user.payment");

        if ($payment->check($request, true)) {
            $player->setPaymentDone(true);
            $em->persist($player);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('app_tournamentmanager_paydone', array('tournament' => $tournament->getId())));
    }

    /**
     * Offer offline payment choices
     * @Route("/{tournament}/user/pay/offline")
     * @Template()
     */
    public function payOfflineAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $manager = $em
            ->getRepository('App\Entity\Tournament:Manager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        // Get global variables
        $globalVars = array();
        $globalKeys = ['payCheckAddress'];
        $globalVars = $em->getRepository('App\Entity\InsaLanGlobalVars')->getGlobalVars($globalKeys);


        return array('tournament' => $tournament, 'user' => $usr, 'manager' => $manager, 'globalVars' => $globalVars);
    }

    /**
     * Allow a manager to drop a pending tournament registration if not managed by team
     * TODO add flashbag confirmation
     * @Route("/{tournament}/user/leave")
     */
    public function leaveAction(Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $manager = $em
            ->getRepository('App\Entity\TournamentManager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        if($manager->getTournament()->getParticipantType() !== "player")
            throw new ControllerException("Not Allowed"); // must be a player only tournament

        // not allowed if he paid something
        if(!$tournament->isFree() && $manager->getPaymentDone()){
            $this->get('session')->getFlashBag()->add('error', "Vous avez payé votre place, merci de contacter l'InsaLan si vous souhaitez vous désister.");
            return $this->redirect($this->generateUrl('app_user_index'));
        }
        // not allowed either if registration are closed
        if(!$tournament->isOpenedNow())
            return $this->redirect($this->generateUrl('app_user_index'));

        $manager->getParticipant()->setManager(null);
        $manager->setParticipant(null);
        $em->remove($manager);
        $em->flush();

        return $this->redirect($this->generateUrl('app_user_index'));
    }

    /**
     * Allow a manager to drop a pending tournament registration managed by teams
     * TODO add flashbag confirmation
     * @Route("/user/leave/team/{teamId}")
     */
    public function leaveTeamAction($teamId) {
        $em = $this->getDoctrine()->getManager();
        $team = $em
            ->getRepository('App\Entity\TournamentTeam')
            ->findOneById($teamId);

        if($team === null)
            return $this->redirect($this->generateUrl('app_user_index'));

        // get targeted manager
        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $manager = $em
            ->getRepository('App\Entity\TournamentManager')
            ->findOneByUserAndPendingTournament($usr, $team->getTournament());

        // is he part of the team roster ?
        if($team->getManager() != $manager)
            return $this->redirect($this->generateUrl('app_user_index'));

        // not allowed if he paid something
        if(!$team->getTournament()->isFree() && $manager->getPaymentDone()){
            $this->get('session')->getFlashBag()->add('error', "Vous avez payé votre place, merci de contacter l'InsaLan si vous souhaitez vous désister.");
            return $this->redirect($this->generateUrl('app_user_index'));
        }
        // not allowed either if registration are closed
        if(!$team->getTournament()->isOpenedNow())
            return $this->redirect($this->generateUrl('app_user_index'));

        $manager->setParticipant(null);
        $team->setManager(null);

        $em->persist($team);
        $em->remove($manager);
        $em->flush();

        return $this->redirect($this->generateUrl('app_user_index'));
    }

}

?>
