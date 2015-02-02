<?php

namespace InsaLan\PizzaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use InsaLan\PizzaBundle\Entity;

class DefaultController extends Controller
{   

    /**
     * @Route("/")
     * @Template()
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();
        $orders = $em->getRepository('InsaLanPizzaBundle:Order')->getAvailable();
        $pizzas = $em->getRepository('InsaLanPizzaBundle:Pizza')->findAll();
        $myOrders = $em->getRepository('InsaLanPizzaBundle:UserOrder')->findBy(array("user" => $user, "paymentDone" => true));

        $ordersChoices = array();

        foreach($orders as $order) {
            $ordersChoices[$order->getId()] = "Le " . $order->getDelivery()->format("d/m à H \h i") . " (moins de " .
                                              10*ceil($order->getAvailableOrders() / 10) . " pizzas disponibles)"; 
        }
        
        $pizzasChoices = array();

        foreach($pizzas as $pizza) {
            $pizzasChoices[$pizza->getId()] = $pizza->getName() . " (" . $pizza->getPrice() . " €)"; 
        }

        $form = $this->createFormBuilder()
                    ->add('order', 'choice', array('choices' => $ordersChoices, 'label' => 'Heure de livraison'))
                    ->add('pizza', 'choice', array('choices' => $pizzasChoices, 'label' => 'Pizza choisie'))
                    ->setAction($this->generateUrl('insalan_pizza_default_index'))
                    ->getForm();

        $form->handleRequest($this->getRequest());
        if($form->isValid()) {

            $data = $form->getData();

            $order = $em->getRepository('InsaLanPizzaBundle:Order')->findOneById($data["order"]);
            $pizza = $em->getRepository('InsaLanPizzaBundle:Pizza')->findOneById($data["pizza"]);

            $userOrder = new Entity\UserOrder();
            $userOrder->setUser($user);
            $userOrder->setPizza($pizza);
            $userOrder->setOrder($order);
            $userOrder->setType(Entity\UserOrder::TYPE_PAYPAL);
            $em->persist($userOrder);
            $em->flush();

            $payment = $this->get("insalan.user.payment");
            $paymentOrder = $payment->getOrder('EUR', $pizza->getPrice());
            $paymentOrder->setUser($user);
            $paymentOrder->addPaymentDetail('Commande Pizza InsaLan', 8, 'Pizza ' . $pizza->getName());

            return $this->redirect($payment->getTargetUrl($paymentOrder, 'insalan_pizza_default_validate', array("id" => $userOrder->getId())));
        }


        return array('myOrders' => $myOrders, 'pizzas' => $pizzas, 'orders' => $orders, 'form' => $form->createView());
    }

    /**
     * @Route("/validate/{id}")
     */
    public function validateAction(Entity\UserOrder $userOrder) {

        $em = $this->getDoctrine()->getManager();
        $payment = $this->get("insalan.user.payment");

        if($payment->check($this->getRequest(), true)) {
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

    /**
     * @Route("/summary")
     * @Template()
     */
    public function summaryAction()
    {
        $em = $this->getDoctrine()->getManager();

        $orders = $em->getRepository('InsaLanPizzaBundle:Order')->getAll();
        foreach($orders as &$order) {
            $pizzas = array();
            foreach ($order->getOrders() as $uo) {
                if(!$uo->getPaymentDone(true))
                    continue;
                if (!isset($pizzas[$uo->getPizza()->getName()]))
                    $pizzas[$uo->getPizza()->getName()] = 0;
                ++$pizzas[$uo->getPizza()->getName()];
            }
            $order->pizzas = $pizzas;
        }

        //$this->get('session')->getFlashBag()->add('info', 'Hey!');
        return array('orders' => $orders);
    }
}
