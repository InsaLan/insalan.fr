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

        $pizzas = $em->getRepository('InsaLanPizzaBundle:Pizza')->findAll();
        //$this->get('session')->getFlashBag()->add('info', 'Hey!');

        return array('pizzas' => $pizzas);
    }

    /**
     * @Route("/order")
     * @Template()
    */
    public function orderAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();
        $order = $em->getRepository('InsaLanPizzaBundle:Order')->getCurrent();

        if ($this->get('request')->getMethod() == 'POST' &&
            ($pizzaId = $this->get('request')->request->get('pizza', false)))
        {
            $pizzaId = (int)$pizzaId;
            $pizza = $em->getRepository('InsaLanPizzaBundle:Pizza')->find($pizzaId);
            if ($pizza)
            {
                $em->getConnection()->beginTransaction();

                $e = new Entity\UserOrder();
                $e->setUser($user);
                $e->setOrder($order);
                $e->setPizza($pizza);
                $e->setDelivered(false);
                $em->persist($e);

                $user->setCredit($user->getCredit() - 1);
                $em->persist($user);

                $em->flush();
                $em->getConnection()->commit();

                $this->get('session')->getFlashBag()->add('info', 'Votre commande a été enregistrée.');
                return $this->redirect($this->generateUrl('insalan_pizza_default_order'));
            }
        }

        $pizzas = $em->getRepository('InsaLanPizzaBundle:Pizza')->findAll();
        $myOrders = $em->getRepository('InsaLanPizzaBundle:UserOrder')->findByUser($user);

        return compact('myOrders', 'pizzas', 'order');
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
