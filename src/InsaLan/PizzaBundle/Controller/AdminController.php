<?php

namespace InsaLan\PizzaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use InsaLan\PizzaBundle\Entity;
use InsaLan\ApiBundle\Http\JsonResponse;

class AdminController extends Controller
{
    /**
     * @Route("/admin")
     * @Route("/admin/{id}")
     * @Template()
     */
    public function indexAction($id = null) {
        $em = $this->getDoctrine()->getManager();
        $order = $formAdd = null;

        $orders = $em->getRepository('InsaLanPizzaBundle:Order')->getAll();
        $ordersChoices = array(null => "");
        foreach($orders as $o) {
            if ($o->getDelivery()->getTimestamp() < mktime() - 3600*24*7) continue;

            // Patch to switch from Symfony2 to Symfony3. We have to switch keys and values in arrays for choices.
            $key = "Le " . $o->getDelivery()->format("d/m à H:i") . " ~ "
                                            . ($o->getCapacity() - $o->getAvailableOrders(false, false))  . " commandes sur "
                                            . $o->getCapacity();
            $ordersChoices[$key] = $o->getId();
        }

        $form = $this->createFormBuilder()
            ->add('order', ChoiceType::class, array(
                  'label' => 'Créneau',
                  'choices_as_values' => true,
                  'choices' => $ordersChoices))
            ->setAction($this->generateUrl('insalan_pizza_admin_index'))
            ->getForm();

        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $data = $form->getData();
            return $this->redirect($this->generateUrl(
                'insalan_pizza_admin_index_1',
                array('id' => $data['order'])));
        }

        if($id) {

            $order = $em->getRepository('InsaLanPizzaBundle:Order')->getOneById($id);

            if(!$order)
                throw new \Exception("Not Available");

            $form->get('order')->submit($order->getId());

            $pizzas = array();
            $sum    = 0;
            foreach ($order->getOrders() as $uo) {
                if(!$uo->getPaymentDone(true))
                    continue;
                if (!isset($pizzas[$uo->getPizza()->getName()]))
                    $pizzas[$uo->getPizza()->getName()] = 0;
                $pizzas[$uo->getPizza()->getName()]++;
                $sum += $uo->getPizza()->getPrice();
            }
            $order->pizzas = $pizzas;
            $order->sum    = $sum;

            $formAdd = $this->getAddUserOrderForm($order)->createView();

        }

        return array('order' => $order, 'form' => $form->createView(), 'formAdd' => $formAdd);
    }

    /**
     * @Route("/admin/{id}/add")
     * @Method({"POST"})
     */
    public function addAction(Entity\Order $order) {

        $em = $this->getDoctrine()->getManager();

        $form = $this->getAddUserOrderForm($order);
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $data = $form->getData();

            $uo = new Entity\UserOrder();
            $uo->setUsernameCanonical($data['username']);
            $uo->setFullnameCanonical($data['fullname']);
            $uo->setPizza($em->getRepository("InsaLanPizzaBundle:Pizza")->findOneById($data['pizza']));
            $uo->setOrder($order);
            $uo->setPaymentDone(true);
            $uo->setType(Entity\UserOrder::TYPE_MANUAL);
            $uo->setPrice($data['price']);

            $user = $em->getRepository("InsaLanUserBundle:User")->findOneByUsername($data['username']);
            if($user)
                $uo->setUser($user);

            $em->persist($uo);
            $em->flush();
        }

        return $this->redirect($this->generateUrl("insalan_pizza_admin_index_1", array("id" => $order->getId())));

    }

    /**
     * @Route("/admin/{id}/lock")
     * @Method({"POST"})
     */
    public function lockAction(Entity\Order $order) {
        $em = $this->getDoctrine()->getManager();
        $order->setClosed(true);
        $em->persist($order);
        $em->flush();
        return $this->redirect($this->generateUrl("insalan_pizza_admin_index_1", array("id" => $order->getId())));
    }

    /**
     * @Route("/admin/{id}/unlock")
     * @Method({"POST"})
     */
    public function unlockAction(Entity\Order $order) {
        $em = $this->getDoctrine()->getManager();
        $order->setClosed(false);
        $em->persist($order);
        $em->flush();
        return $this->redirect($this->generateUrl("insalan_pizza_admin_index_1", array("id" => $order->getId())));
    }

    /**
     * @Route("/admin/order/{id}/remove")
     * @Method({"POST"})
     */
    public function order_removeAction(Entity\UserOrder $uo) {

        $em = $this->getDoctrine()->getManager();

        $id = $uo->getOrder()->getId();

        $em->remove($uo);
        $em->flush();

        return $this->redirect($this->generateUrl("insalan_pizza_admin_index_1", array("id" => $id)));
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

    /////////////// PRIVATE //////////

    private function getAddUserOrderForm(Entity\Order $o) {

        $em = $this->getDoctrine()->getManager();

        $pizzas = $em->getRepository('InsaLanPizzaBundle:Pizza')->findAll();
        $pizzasChoices = array();

        // Patch to switch from Symfony2 to Symfony3. We have to switch keys and values in arrays for choices.
        foreach($pizzas as $pizza) {
            $key = $pizza->getName() . " (" . $pizza->getPrice() . " €)";
            $pizzasChoices[$key] = $pizza->getId();
        }

        return $this->createFormBuilder()
                    ->add('username', TextType::class, array('label' => 'Pseudonyme', 'required' => false))
                    ->add('fullname', TextType::class, array('label' => 'Prénom NOM', 'required' => true))
                    ->add('pizza', ChoiceType::class, array(
                        'choices_as_values' => true,
                        'choices' => $pizzasChoices,
                        'label' => 'Pizza'))
                    ->add('price', ChoiceType::class, array(
                        'choices_as_values' => true,
                        'choices' =>
                            array(
                                'Joueur' => Entity\UserOrder::FULL_PRICE,
                                'Staff' => Entity\UserOrder::STAFF_PRICE,
                                'Gratuit' => Entity\UserOrder::FREE_PRICE
                            ),
                        'label' => 'Tarif'))
                    ->setAction($this->generateUrl('insalan_pizza_admin_add', array('id' => $o->getId())))
                    ->getForm();
    }

}
