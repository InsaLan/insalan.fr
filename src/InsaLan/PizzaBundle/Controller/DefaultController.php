<?php

namespace InsaLan\PizzaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use InsaLan\PizzaBundle\Entity;
use InsaLan\UserBundle\Entity\PaymentDetails;

class DefaultController extends Controller
{

    /**
     * @Route("/")
     * @Template()
    */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $paypalIncrease = Entity\UserOrder::PAYPAL_INCREASE;

        $user = $this->get('security.token_storage')->getToken()->getUser();

        if ($user->getFirstname() == null || $user->getFirstname() == "" || $user->getLastname() == null || $user->getLastname() == "" || $user->getPhoneNumber() == null || $user->getPhoneNumber() == "" || $user->getBirthdate() == null) {
            $this->get('session')->getFlashBag()->add(
                'info',
                'Merci de remplir ces informations avant toute commande...'
            );
            return $this->redirect($this->generateUrl('insalan_user_default_index'));
        }

        $orders = $em->getRepository('InsaLanPizzaBundle:Order')->getAvailable($user);
        $pizzas = $em->getRepository('InsaLanPizzaBundle:Pizza')->findAll();
        $myOrders = $em->getRepository('InsaLanPizzaBundle:UserOrder')->getByUser($user);

        $ordersChoices = array();

        foreach($orders as $order) {
            $a = 10*round($order->getAvailableOrders() / 10);
            if($a <= 0)
                $a = 10;
            // Patch to switch from Symfony2 to Symfony3. We have to switch keys and values in arrays for choices.
            $ordersKey = "Le " . $order->getDelivery()->format("d/m à H \h i") . " (moins de " .
                                              $a . " pizzas disponibles)";
            $ordersChoices[$ordersKey] = $order->getId();
        }

        $pizzasChoices = array();

        foreach($pizzas as $pizza) {
            $pizzaKey = $pizza->getName() . " (" . $pizza->getPrice() . " € + " . $paypalIncrease . " €)";
            $pizzasChoices[$pizzaKey] = $pizza->getId();
        }

        $form = $this->createFormBuilder()
                    ->add('order', ChoiceType::class, array(
                          'choices_as_values' => true,
                          'choices' => $ordersChoices,
                          'label' => 'Heure de livraison'))
                    ->add('pizza', ChoiceType::class, array(
                          'choices_as_values' => true,
                          'choices' => $pizzasChoices,
                          'label' => 'Pizza choisie'))
                    ->setAction($this->generateUrl('insalan_pizza_default_index'))
                    ->getForm();

        $form->handleRequest($request);
        if($form->isValid()) {

            $data = $form->getData();

            $order = $em->getRepository('InsaLanPizzaBundle:Order')->findOneById($data["order"]);
            $pizza = $em->getRepository('InsaLanPizzaBundle:Pizza')->findOneById($data["pizza"]);
            $foreign = $em->getRepository('InsaLanPizzaBundle:Order')->isForeignUser($user);

            $userOrder = new Entity\UserOrder();
            $userOrder->setUser($user);
            $userOrder->setPizza($pizza);
            $userOrder->setOrder($order);
            $userOrder->setType(Entity\UserOrder::TYPE_PAYPAL);
            $userOrder->setForeign($foreign);
            $em->persist($userOrder);
            $em->flush();

            $payment = $this->get("insalan.user.payment");
            $paymentOrder = $payment->getOrder('EUR', $pizza->getPrice() + $paypalIncrease);
            $paymentOrder->setUser($user);
            $paymentOrder->setRawPrice($pizza->getPrice());
            $paymentOrder->setPlace(PaymentDetails::PLACE_WEB);
            $paymentOrder->setType(PaymentDetails::TYPE_PAYPAL);
            $paymentOrder->addPaymentDetail('Commande Pizza InsaLan #' . $userOrder->getId(), $pizza->getPrice(), 'Pizza ' . $pizza->getName());
            $paymentOrder->addPaymentDetail('Majoration paiement en ligne', $paypalIncrease, 'Frais de gestion du paiement');

            return $this->redirect($payment->getTargetUrl($paymentOrder, 'insalan_pizza_default_validate', array("id" => $userOrder->getId())));
        }


        return array('myOrders' => $myOrders, 'pizzas' => $pizzas, 'orders' => $orders, 'form' => $form->createView());
    }

    /**
     * @Route("/validate/{id}")
     */
    public function validateAction(Entity\UserOrder $userOrder, Request $request) {

        $em = $this->getDoctrine()->getManager();
        $payment = $this->get("insalan.user.payment");

        if($payment->check($request, true)) {
            $userOrder->setPaymentDone(true);
            $em->persist($userOrder);
            $hour = $userOrder->getOrder()->getDelivery()->format("H \h i");
            $this->get('session')->getFlashBag()->add('info', "Commande réalisée avec succès ! Votre pizza sera livrée vers $hour." );
        } else {
            $em->remove($userOrder);
            $this->get('session')->getFlashBag()->add('error', "Erreur de paiement. En cas de problème, veuillez contacter un membre du staff rapidement.");
        }

        $em->flush();

        return $this->redirect($this->generateUrl("insalan_pizza_default_index"));

    }
}
