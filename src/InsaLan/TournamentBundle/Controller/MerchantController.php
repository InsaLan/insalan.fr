<?php

namespace InsaLan\TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use InsaLan\TournamentBundle\Entity;
use InsaLan\TournamentBundle\Entity\Player;
use InsaLan\TournamentBundle\Exception\ControllerException;

use InsaLan\UserBundle\Entity\MerchantOrder;

use InsaLan\ApiBundle\Http\JsonResponse;

class MerchantController extends Controller
{

    /**
     * @Route("/merchant")
     * @Route("/{id}/merchant", requirements={"id" = "\d+"})
     * @Template()
     */
    public function indexAction($id = null)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $tournaments = $em->getRepository('InsaLanTournamentBundle:Tournament')->findAll();
        $a = array(null => '');
        foreach ($tournaments as $t) {
            if ($t->isOpenedNow())
                $a[$t->getId()] = $t->getName();
        }

        $form = $this->createFormBuilder()
            ->add('tournament', 'choice', array('label' => 'Tournoi', 'choices' => $a))
            ->setAction($this->generateUrl('insalan_tournament_merchant_index'))
            ->getForm();


        $tournament = $data = null;
        $players = $pendingPlayers = $paidPlayers = $previousPaidPlayers = $discounts = array();
        $total = 0;

        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $data = $form->getData();
            return $this->redirect($this->generateUrl(
                'insalan_tournament_merchant_index_1',
                array('id' => $data['tournament'])));
        }
        else if (null !== $id) {
            $data = array('tournament' => $id);
            $form->get('tournament')->submit($id);

            foreach ($tournaments as &$t) {
                if ($t->getId() == $data['tournament']) {
                    $tournament = $t;
                }
            }

            if (null === $tournament) {
                throw new NotFoundHttpException('InsaLan\\TournamentBundle\\Entity\\Tournament object not found.');;
            }

            $players = $em->getRepository('InsaLanTournamentBundle:Participant')
                          ->findByTournament($tournament);

            foreach ($players as &$p) {
                if ($p->getParticipantType() == "team"){
                    foreach ($p->getPlayers() as &$player) {
                        if (!$player->getPaymentDone())
                            $pendingPlayers[] = $player;
                    }
                }else{
                    if (!$p->getPaymentDone())
                        $pendingPlayers[] = $p;
                }
            }

            $discounts = $em->getRepository('InsaLanUserBundle:Discount')
                            ->findByTournament($tournament);
        }

        $allPaidPlayers = $em->getRepository('InsaLanUserBundle:MerchantOrder')
                        ->findByMerchant($user);

        foreach ($allPaidPlayers as &$order) {
            $paidParticipant = $em->getRepository('InsaLanTournamentBundle:Participant')->findByUser($order->getPlayer()->getUser());

            foreach ($paidParticipant as &$p) {
                if ($p->getParticipantType() == "team"){
                    foreach ($p->getPlayers() as &$player) {
                        if ($player->getId() == $order->getPlayer()->getId()){
                            $order->getPlayer()->setTournament($p->getTournament());
                            $order->getPlayer()->setValidationDate($p->getValidationDate());
                            break;
                        }
                    }
                }
            }

            if ($order->getPlayer()->getTournament()->isOpenedNow() || $order->getPlayer()->getTournament()->isPending()) {
                $total += $order->getPayment()["L_PAYMENTREQUEST_0_AMT0"];
                $paidPlayers[] = $order;
            }
            else {
                $previousPaidPlayers[] = $order;
            }
        }

        $output = array(
            'form'        => $form->createView(),
            'tournament'  => $tournament,
            'players'     => $pendingPlayers,
            'paidPlayers' => $paidPlayers,
            'previousPaidPlayers' => $previousPaidPlayers,
            'discounts'   => $discounts,
            'user'        => $user,
            'total'      => $total,
        );

        return $output;
    }

    /**
     * @Route("/{id}/merchant/{player}/validate", requirements={"id" = "\d+"})
     * @Route("/{id}/merchant/{player}/discount/{discount}/validate", requirements={"id" = "\d+"})
     */
    public function validateAction(Entity\Tournament $tournament, Entity\Player $player, $discount = null){
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        if ($player->getUser() == null){
            $this->get('session')->getFlashBag()->add('error', "Impossible de valider cette place (aucun utilisateur associé à la commande)");
            return $this->redirect($this->generateUrl('insalan_tournament_merchant_index_1', array('id' => $tournament->getId())));
        }

        $player->setPaymentDone(true);

        $storage =  $this->get('payum')->getStorage('InsaLan\UserBundle\Entity\PaymentDetails');
        $order = $storage->createModel();
        $order->setUser($player->getUser());

        $price = $tournament->getWebPrice();
        $title = 'Place pour le tournoi '.$tournament->getName();

        if ($discount !== null){
            $discount = $em->getRepository('InsaLanUserBundle:Discount')
                            ->findOneById($discount);

            if ($discount->getTournament()->getId() !== $tournament->getId()){
                $this->get('session')->getFlashBag()->add('error', "discount not allowed");
                return $this->redirect($this->generateUrl('insalan_tournament_merchant_index_1', array('id' => $tournament->getId())));
            }

            $price -= $discount->getAmount();
            $title += " (" + $discount->getName() + ")";
        }

        $order->setDiscount($discount);

        $order['PAYMENTREQUEST_0_CURRENCYCODE'] = $tournament->getCurrency();
        $order['PAYMENTREQUEST_0_AMT'] = $price;

        $order['L_PAYMENTREQUEST_0_NAME0'] = $title;
        $order['L_PAYMENTREQUEST_0_AMT0'] = $price;
        $order['L_PAYMENTREQUEST_0_DESC0'] = $tournament->getDescription();
        $order['L_PAYMENTREQUEST_0_NUMBER0'] = 1;

        $order['L_PAYMENTREQUEST_0_NAME1'] = 'Paiement dans un point de vente partenaire';
        $order['L_PAYMENTREQUEST_0_AMT1'] = 0;
        $order['L_PAYMENTREQUEST_0_DESC1'] = 'Paiement validé par '.$user->getFirstName().' '.$user->getLastName();
        $order['L_PAYMENTREQUEST_0_NUMBER1'] = 1;

        $storage->updateModel($order);

        $merchantOrder = new MerchantOrder();
        $merchantOrder->setMerchant($user);
        $merchantOrder->setPlayer($player);
        $merchantOrder->setPayment($order);

        $em->persist($player);
        $em->persist($merchantOrder);
        $em->flush();

        return $this->redirect($this->generateUrl(
            'insalan_tournament_merchant_index_1',
            array('id' => $tournament->getId())));
    }
}
