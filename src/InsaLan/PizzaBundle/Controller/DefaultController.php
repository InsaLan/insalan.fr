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
        $myOrders = $em->getRepository('InsaLanPizzaBundle:UserOrder')->findByUser($user);

        $ordersChoices = array();

        foreach($orders as $order) {
            $ordersChoices[$order->getId()] = "Le " . $order->getDelivery()->format("d/m à H \h i"); 
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
            // Payment generation
            // TODO
            
            die("Not Yet Implemented");
        }


        return array('myOrders' => $myOrders, 'pizzas' => $pizzas, 'orders' => $orders, 'form' => $form->createView());
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
