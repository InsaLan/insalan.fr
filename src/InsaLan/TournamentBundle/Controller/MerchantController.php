<?php

namespace InsaLan\TournamentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use InsaLan\TournamentBundle\Entity;
use InsaLan\TournamentBundle\Entity\Player;
use InsaLan\TournamentBundle\Exception\ControllerException;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use InsaLan\UserBundle\Entity\MerchantOrder;
use InsaLan\UserBundle\Entity\PaymentDetails;

use InsaLan\ApiBundle\Http\JsonResponse;

class MerchantController extends Controller
{

    /**
     * @Route("/merchant")
     * @Route("/{id}/merchant", requirements={"id" = "\d+"})
     * @Template()
     */
    public function indexAction($id = null, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $registrables = $em->getRepository('InsaLanTournamentBundle:Registrable')->findAll();

        // Patch to switch from Symfony2 to Symfony3. We have to switch keys and values in arrays for choices.
        $a = array(null => '');
        foreach ($registrables as $t) {
            if ($t->isOpenedNow())
                $a[$t->getName()] = $t->getId();
        }

        $form = $this->createFormBuilder()
            ->add('registrable', ChoiceType::class, array(
                  'label' => 'Tournoi',
                  'choices' => $a))
            ->setAction($this->generateUrl('insalan_tournament_merchant_index'))
            ->getForm();

        $registrable = $data = null;
        $players = $pendingPlayers = $paidPlayers = $previousPaidPlayers = $discounts = array();
        $total = 0;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            return $this->redirect($this->generateUrl(
                'insalan_tournament_merchant_index_1',
                array('id' => $data['registrable'])));
        }
        else if (null !== $id) {
            $data = array('registrable' => $id);
            $form->get('registrable')->submit($id);

            foreach ($registrables as &$t) {
                if ($t->getId() == $data['registrable']) {
                    $registrable = $t;
                }
            }

            if (null === $registrable) {
                throw new NotFoundHttpException('InsaLan\\TournamentBundle\\Entity\\Registrable object not found.');;
            }

            $players = $em->getRepository('InsaLanTournamentBundle:Participant')
                          ->findByRegistrable($registrable);

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
                            ->findByRegistrable($registrable);
        }

        $allPaidPlayers = $em->getRepository('InsaLanUserBundle:MerchantOrder')
                        ->findByMerchant($user);

        foreach ($allPaidPlayers as &$order) {
            $paidParticipant = [];
            foreach ($order->getPlayers() as $p) {
                foreach ($em->getRepository('InsaLanTournamentBundle:Participant')->findByUser($p->getUser()) as $pp)
                    $paidParticipant[] = $pp;
            }

            foreach ($paidParticipant as &$p) {
                if ($p->getParticipantType() == "team"){
                    foreach ($p->getPlayers() as &$player) {
                        if ($order->hasPlayer($player)){
                            $player->setTournament($p->getTournament());
                            $player->setValidationDate($p->getValidationDate());
                            break;
                        }
                    }
                }
            }

            foreach ($order->getPlayers() as $p) {
                if ($p->getRegistrable()->isOpenedNow() || $p->getRegistrable()->isPending()) {
                    $total += $order->getPayment()["L_PAYMENTREQUEST_0_AMT0"];
                    $paidPlayers[] = $order;
                }
                else {
                    $previousPaidPlayers[] = $order;
                }
                break;
            }
        }

        $output = array(
            'form'        => $form->createView(),
            'registrable'  => $registrable,
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
    public function validateAction(Entity\Registrable $registrable, Entity\Player $player, $discount = null){
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if ($player->getUser() == null){
            $this->get('session')->getFlashBag()->add('error', "Impossible de valider cette place (aucun utilisateur associé à la commande)");
            return $this->redirect($this->generateUrl('insalan_tournament_merchant_index_1', array('id' => $registrable->getId())));
        }

        $price = $registrable->getWebPrice();
        $title = 'Place pour le tournoi '.$registrable->getName();

        if ($discount !== null){
            $discount = $em->getRepository('InsaLanUserBundle:Discount')
                            ->findOneById($discount);

            if ($discount->getRegistrable()->getId() !== $registrable->getId()){
                $this->get('session')->getFlashBag()->add('error', "discount not allowed");
                return $this->redirect($this->generateUrl('insalan_tournament_merchant_index_1', array('id' => $registrable->getId())));
            }

            $price -= $discount->getAmount();
            $title .= " (" . $discount->getName() . ")";
        }

        $payment = $this->get("insalan.user.payment");
        $order = $payment->getOrder($registrable->getCurrency(), $price);
        $order->setUser($player->getUser());

        $order->setDiscount($discount);

        $order->setRawPrice($price);
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) { // The user is in InsaLan's staff so it is a preorder by check
            $order->setPlace(PaymentDetails::PLACE_WEB);
            $order->setType(PaymentDetails::TYPE_CHECK);
            } else { // User is a partner
                $order->setPlace(PaymentDetails::PLACE_IN_PARTNER_SHOP);
                $order->setType(PaymentDetails::TYPE_UNDEFINED); // TODO Save payment type
            }

        $order->addPaymentDetail($title, $price, '');
        $order->addPaymentDetail('Paiement dans un point de vente partenaire', 0, 'Paiement validé par '.$user->getFirstName().' '.$user->getLastName());

        $payment->update($order);

        $merchantOrder = new MerchantOrder();
        $merchantOrder->setMerchant($user);
        $merchantOrder->addPlayer($player);
        $merchantOrder->setPayment($order);

        // MerchantOrder need to be persisted before Player's payment validation
        $em->persist($merchantOrder);
        $em->persist($player);
        $em->flush();

        $player->setPaymentDone(true);

        $em->persist($player);
        $em->flush();

        return $this->redirect($this->generateUrl(
            'insalan_tournament_merchant_index_1',
            array('id' => $registrable->getId())));
    }
}
