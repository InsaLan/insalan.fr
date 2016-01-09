<?php

/**
 * ManagerController
 * All the stuff related to managers
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

use InsaLan\TournamentBundle\Form\SetManagerName;
use InsaLan\TournamentBundle\Form\TeamLoginType;
use InsaLan\TournamentBundle\Exception\ControllerException;

use InsaLan\TournamentBundle\Entity\Manager;
use InsaLan\TournamentBundle\Entity\Participant;
use InsaLan\TournamentBundle\Entity\Tournament;
use InsaLan\TournamentBundle\Entity\Team;
use InsaLan\TournamentBundle\Entity;

/**
* ManagerController
* All the stuff realted to managers management
* @Route("/manager")
*/
class ManagerController extends Controller
{

    /**
     * Manage all steps for registering into a tournament as manager
     * @Route("/{tournament}/user/enroll")
     */
    public function enrollAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.context')->getToken()->getUser();

        $manager = $em
            ->getRepository('InsaLanTournamentBundle:Manager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        if ($manager === null)
            return $this->redirect($this->generateUrl('insalan_tournament_manager_setname',array('tournament' => $tournament->getId())));
        else if ($tournament->getParticipantType() === 'team' && $manager->getParticipant() === null)
            return $this->redirect($this->generateUrl('insalan_tournament_manager_jointeamwithpassword',array('tournament' => $tournament->getId())));
        else if (!$manager->getPaymentDone())
            return $this->redirect($this->generateUrl('insalan_tournament_manager_pay',array('tournament' => $tournament->getId())));
        else
            return $this->redirect($this->generateUrl('insalan_tournament_manager_paydone',array('tournament' => $tournament->getId())));
    }

    /**
     * Create a new manager related to a tournament
     * @Route("/{tournament}/user/set")
     * @Template()
     */
    public function setNameAction(Request $request, Entity\Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();

        $manager = $em->getRepository('InsaLanTournamentBundle:Manager')->findOneByUserAndPendingTournament($usr, $tournament);

        if ($manager === null) {
            $manager = new Manager();
            $manager->setUser($usr);
            $manager->setTournament($tournament);
        }

        $form = $this->createForm(new SetManagerName(), $manager);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($manager);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('insalan_tournament_manager_jointeamwithpassword', array('tournament' => $tournament->getId()))
            );
        }

        return array('form' => $form->createView(), 'selectedGame' => $tournament->getType(), 'tournamentId' => $tournament->getId());
    }

    /**
     * Allow a new manager to join a team with name and password
     * @Route("/{tournament}/user/setname")
     * @Template()
     */
    public function joinTeamWithPasswordAction(Request $request, Entity\Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();

        // handle only team tournaments
        if($tournament->getParticipantType() !== "team")
            throw new ControllerException("Équipes non acceptées dans ce tournois");

        // check if there is already a pending manager for this user and tournament
        $manager = $em->getRepository('InsaLanTournamentBundle:Manager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        if($manager === null)
            return $this->redirect($this->generateUrl('insalan_tournament_manager_setname', array('tournament' => $tournament->getId())));

        $form_team = new Team();
        $form = $this->createForm(new TeamLoginType(), $form_team); // fill name and plainPassword
        $form->handleRequest($request);

        // inspired by UserController::joinExistingTeam
        // TODO rework this by putting the password hash into the request ?
        $error_details = null;
        if ($form->isValid()) {
            try {
                // hash password
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($usr);
                $form_team->setPassword($encoder->encodePassword($form_team->getPlainPassword(), sha1('pleaseHashPasswords'.$form_team->getName())));
                $team = $em
                    ->getRepository('InsaLanTournamentBundle:Team')
                    ->findOneByNameAndTournament($form_team->getName(), $tournament);

                if ($team === null || $team->getTournament()->getId() !== $tournament->getId())
                    throw new ControllerException("Équipe invalide");

                if ($team->getPassword() === $form_team->getPassword()) {
                    // denied if there is already a manager in the team
                    if ($team->getManager() != null)
                        throw new ControllerException("L'équipe a déjà un manager");

                    // Because PHP don't support polymorphism, we must get the corresponding Participant object
                    $team_participant = $em
                        ->getRepository('InsaLanTournamentBundle:Participant')->
                        findOneById($team->getId());
                    $manager->setParticipant($team_participant);
                    $team->setManager($manager);
                    $em->persist($manager);
                    $em->persist($team);
                    $em->flush();

                    return $this->redirect($this->generateUrl('insalan_tournament_manager_pay', array('tournament' => $tournament->getId())));

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

        $usr = $this->get('security.context')->getToken()->getUser();
        $manager = $em
            ->getRepository('InsaLanTournamentBundle:Manager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        if($tournament->isFree()) {
            $manager->setPaymentDone(true);
            $em->persist($manager);
            $em->flush();
            return $this->redirect($this->generateUrl('insalan_tournament_manager_paydone', array("tournament" => $tournament->getId())));
        }

        return array('tournament' => $tournament, 'user' => $usr, 'manager' => $manager);
    }

    /**
     * Paypal stuff
     * @Route("/{tournament}/user/pay/paypal_ec")
     */
    public function payPaypalECAction(Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.context')->getToken()->getUser();
        $manager = $em
            ->getRepository('InsaLanTournamentBundle:Manager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        $paymentName = 'paypal_express_checkout_and_doctrine_orm';

        $price = ($manager::ONLINE_PRICE + $tournament->getOnlineIncreaseInPrice());

        $storage =  $this->get('payum')->getStorage('InsaLan\UserBundle\Entity\PaymentDetails');
        $order = $storage->createModel();
        $order->setUser($usr);

        $order['PAYMENTREQUEST_0_CURRENCYCODE'] = $tournament->getCurrency();
        $order['PAYMENTREQUEST_0_AMT'] = $price;

        $order['L_PAYMENTREQUEST_0_NAME0'] = 'Place manager pour le tournoi '.$tournament->getName();
        $order['L_PAYMENTREQUEST_0_AMT0'] = $manager::ONLINE_PRICE;
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
            'insalan_tournament_manager_paydonetemp',
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
        $manager = $em
            ->getRepository('InsaLanTournamentBundle:Manager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        return array('tournament' => $tournament, 'user' => $usr, 'manager' => $manager);
    }

    /**
     * Perform payment validation
     * @Route("/{tournament}/user/pay/done_temp")
     */
    public function payDoneTempAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Manager')
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
        return $this->redirect($this->generateUrl('insalan_tournament_manager_paydone', array('tournament' => $tournament->getId())));
    }

    /**
     * Offer offline payment choices
     * @Route("/{tournament}/user/pay/offline")
     * @Template()
     */
    public function payOfflineAction(Request $request, Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();
        $manager = $em
            ->getRepository('InsaLanTournamentBundle:Manager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        return array('tournament' => $tournament, 'user' => $usr, 'manager' => $manager);
    }
    
    /**
     * Allow a manager to drop a pending tournament registration if not managed by team
     * @Route("/{tournament}/user/leave")
     */
    public function leaveAction(Entity\Tournament $tournament) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.context')->getToken()->getUser();
        $manager = $em
            ->getRepository('InsaLanTournamentBundle:Manager')
            ->findOneByUserAndPendingTournament($usr, $tournament);

        if($manager->getTournament()->getParticipantType() !== "player")
            throw new ControllerException("Not Allowed"); // must be a player only tournament

        $em->remove($manager);
        $em->flush();

        return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
    }

    /**
     * Allow a manager to drop a pending tournament registration managed by teams
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

        // get targeted manager
        $usr = $this->get('security.context')->getToken()->getUser();
        $manager = $em
            ->getRepository('InsaLanTournamentBundle:Manager')
            ->findOneByUserAndPendingTournament($usr, $team->getTournament());

        // is he part of the team roster ?
        if($team->getManager() != $manager)
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

        // not allowed if he paid something
        if(!$team->getTournament()->isFree() && $manager->getPaymentDone()){
            $this->get('session')->getFlashBag()->add('error', "Vous avez payé votre place, merci de contacter l'InsaLan si vous souhaitez vous désister.");
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        }
        // not allowed either if registration are closed
        if(!$team->getTournament()->isOpenedNow())
            return $this->redirect($this->generateUrl('insalan_tournament_user_index')); 

        $manager->setParticipant(null);
        $team->setManager(null);

        $em->persist($team);
        $em->remove($manager);
        $em->flush();
        return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
    }

}

?>
