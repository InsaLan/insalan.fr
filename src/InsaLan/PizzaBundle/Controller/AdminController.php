<?php

namespace InsaLan\PizzaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use InsaLan\PizzaBundle\Entity;
use InsaLan\ApiBundle\Http\JsonResponse;

class AdminController extends Controller
{
    /**
     * @Route("/admin")
     * @Template()
     */
    public function indexAction()
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

        return array('orders' => $orders);
    }

    /**
     * @Route("/admin/order/{id}/remove")
     * @Method({"POST"})
     */
    public function order_removeAction(Entity\UserOrder $uo) {

        $em = $this->getDoctrine()->getManager();

        $em->remove($uo);
        $em->flush();

        return $this->redirect($this->generateUrl("insalan_pizza_admin_index"));
    }
    
    /**
     * @Route("/admin/order/{id}/status/{status}")
     * @Method({"POST"})
     */
    public function order_statusAction(Entity\UserOrder $uo, $status) {

        $em = $this->getDoctrine()->getManager();

        $status = intval($status);

        $uo->setDelivered($status === 1);
        $em->persist($uo);
        $em->flush();

        return new JsonResponse(array("err" => null));

    }

}
