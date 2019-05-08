<?php

namespace InsaLan\PizzaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
   * @Route("/admin/pizza")
   * @Template()
   */
  public function pizzaAction() {
    $em = $this->getDoctrine()->getManager();
    $pizzas = $em->getRepository('InsaLanPizzaBundle:Pizza')->findAll();
    $formAdd = $this->getAddPizzaForm()->createView();
    return array(
      'pizzas' => $pizzas,
      'formAdd' => $formAdd,
    );
  }

  /**
   * @Route("/admin/pizza/add")
   * @Method({"POST"})
   */
  public function pizza_addAction() {

    $em = $this->getDoctrine()->getManager();
    $form = $this->getAddPizzaForm();
    $form->handleRequest($this->getRequest());
    $data = $form->getData();

    if ($form->isValid()) {
      $pizza = new Entity\Pizza();
      $pizza->setName($data['pizzaName']);
      $pizzaPrice = $data['pizzaPrice'];
      $pizza->setPrice($pizzaPrice);
      $pizza->setVeggie($data['pizzaVeggie']);
      if ($data['pizzaDescription'] == null) {
        $pizza->setDescription("");
      } else {
        $pizza->setDescription($data['pizzaDescription']);
      }

      $em->persist($pizza);
      $em->flush();
    }

    return $this->redirect($this->generateUrl("insalan_pizza_admin_pizza"));
  }

  /**
   * @Route("/admin/pizza/{id}/remove")
   * @Method({"POST"})
   */
  public function pizza_removeAction(Entity\Pizza $pizza) {

      $em = $this->getDoctrine()->getManager();
      $em->remove($pizza);
      $em->flush();

      return $this->redirect($this->generateUrl("insalan_pizza_admin_pizza"));
  }


  /**
   * @Route("/admin/creneau")
   * @Template()
   */
  public function creneauAction() {
    $em = $this->getDoctrine()->getManager();
    $order = $em->getRepository('InsaLanPizzaBundle:Order')->findAll();
    $formAdd = $this->getAddOrderForm()->createView();
    return array(
      'orders' => $order,
      'formAdd' => $formAdd,
    );
  }

  /**
   * @Route("/admin/creneau/add")
   * @Method({"POST"})
   */
  public function creneau_addAction() {

    $em = $this->getDoctrine()->getManager();
    $form = $this->getAddOrderForm();
    $form->handleRequest($this->getRequest());
    $data = $form->getData();
    if ($form->isValid()) {
      $order = new Entity\Order();
      $order->setExpiration($data['orderExpirationDateTime']);
      $order->setDelivery($data['orderDeliveryDateTime']);
      $order->setCapacity(intval($data['orderCapacity']));
      $order->setForeignCapacity(intval($data['orderForeignCapacity']));
      $order->setClosed($data['orderClosed']);
      $em->persist($order);
      $em->flush();
    }

    return $this->redirect($this->generateUrl("insalan_pizza_admin_creneau"));
  }

  /**
   * @Route("/admin/creneau/{id}/remove")
   * @Method({"POST"})
   */
  public function creneau_removeAction(Entity\Order $order) {

      $em = $this->getDoctrine()->getManager();
      $em->remove($order);
      $em->flush();

      return $this->redirect($this->generateUrl("insalan_pizza_admin_creneau"));
  }

    /**
     * @Route("/admin/commande")
     * @Route("/admin/commande/{id}")
     * @Template()
     */
    public function commandeAction(Request $request, $id = null) {
        $em = $this->getDoctrine()->getManager();
        $order = $formAdd = null;

        $showAll = $request->query->get("showAll", false);

        $orders = $em->getRepository('InsaLanPizzaBundle:Order')->getAll();
        $ordersChoices = array(null => "");
        foreach($orders as $o) {
            if (!$showAll && $o->getDelivery()->getTimestamp() < time() - 3600*24*7) continue;

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
            ->setAction($this->generateUrl('insalan_pizza_admin_commande', ['showAll' => $showAll]))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            return $this->redirect($this->generateUrl(
                'insalan_pizza_admin_commande_1',
                array('id' => $data['order'], 'showAll' => $showAll)));
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

        return array(
            'order' => $order,
            'form' => $form->createView(),
            'formAdd' => $formAdd,
            'showAll' => $showAll,
        );
    }

    /**
     * @Route("/admin/{id}/add")
     * @Method({"POST"})
     */
    public function addAction(Entity\Order $order, Request $request) {

        $em = $this->getDoctrine()->getManager();

        $form = $this->getAddUserOrderForm($order);
        $form->handleRequest($request);

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

        return $this->redirect($this->generateUrl("insalan_pizza_admin_commande_1", array("id" => $order->getId())));

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
        return $this->redirect($this->generateUrl("insalan_pizza_admin_commande_1", array("id" => $order->getId())));
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
        return $this->redirect($this->generateUrl("insalan_pizza_admin_commande_1", array("id" => $order->getId())));
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

        return $this->redirect($this->generateUrl("insalan_pizza_admin_commande_1", array("id" => $id)));
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

    private function getAddPizzaForm() {
        return $this->createFormBuilder()
                    ->add('pizzaName', 'text', array('label' => 'Nom', 'required' => true))
                    ->add('pizzaPrice', 'number', array('label' => 'Prix', 'required' => true))
                    ->add('pizzaVeggie', 'checkbox', array('label' => 'Veggie', 'required' => false))
                    ->add('pizzaDescription', 'text', array('label' => 'Description', 'required' => false))
                    ->setAction($this->generateUrl('insalan_pizza_admin_pizza_add'))
                    ->getForm();
    }

    private function getAddOrderForm() {
        return $this->createFormBuilder()
                    ->add('orderExpirationDateTime', 'datetime', array('required' => true))
                    ->add('orderDeliveryDateTime', 'datetime', array('required' => true))
                    ->add('orderCapacity', 'number', array('label' => 20, 'required' => false))
                    ->add('orderForeignCapacity', 'number', array('label' => 3, 'required' => false))
                    ->add('orderClosed', 'checkbox', array('required' => false))
                    ->setAction($this->generateUrl('insalan_pizza_admin_creneau_add'))
                    ->getForm();
    }

}
