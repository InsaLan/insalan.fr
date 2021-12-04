<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use App\Entity;
use App\Http\JsonResponse;
/**
 * @Route("/admin")
 */
class AdminPizzaController extends Controller
{

  /**
   * @Route("/pizza")
   * @Template()
   */
  public function pizzaAction() {
    $em = $this->getDoctrine()->getManager();
    $pizzas = $em->getRepository('App\Entity\Pizza')->findAll();
    $formAdd = $this->getAddPizzaForm()->createView();
    return array(
      'pizzas' => $pizzas,
      'formAdd' => $formAdd,
    );
  }

  /**
   * @Route("/pizza/add")
   * @Method({"POST"})
   */
  public function pizza_addAction(Request $request) {

    $em = $this->getDoctrine()->getManager();
    $form = $this->getAddPizzaForm();
    $form->handleRequest($request);
    $data = $form->getData();

    if ($form-> isSubmitted() && $form->isValid()) {
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

    return $this->redirect($this->generateUrl("app_adminpizza_pizza"));
  }

  /**
   * @Route("/pizza/{id}/remove")
   * @Method({"POST"})
   */
  public function pizza_removeAction(Entity\Pizza $pizza) {

      try {
        $em = $this->getDoctrine()->getManager();
        $em->remove($pizza);
        $em->flush();
      } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException  $e) {
        $this->get('session')->getFlashBag()->add('error', "Suppression impossible : des commandes sont associées à cette pizza.");
      }


      return $this->redirect($this->generateUrl("app_adminpizza_pizza"));
  }


  /**
   * @Route("/creneau")
   * @Template()
   */
  public function creneauAction() {
    $em = $this->getDoctrine()->getManager();
    $order = $em->getRepository('App\Entity\PizzaOrder')->findBy([], ['id' => 'DESC']);
    $formAdd = $this->getAddOrderForm()->createView();
    return array(
      'orders' => $order,
      'formAdd' => $formAdd,
    );
  }

  /**
   * @Route("/creneau/add")
   * @Method({"POST"})
   */
  public function creneau_addAction(Request $request) {

    $em = $this->getDoctrine()->getManager();
    $form = $this->getAddOrderForm();
    $form->handleRequest($request);
    $data = $form->getData();
    if ($form-> isSubmitted() && $form->isValid()) {
      $order = new Entity\PizzaOrder();
      $order->setExpiration($data['orderExpirationDateTime']);
      $order->setDelivery($data['orderDeliveryDateTime']);
      $order->setCapacity(intval($data['orderCapacity']));
      $order->setForeignCapacity(intval($data['orderForeignCapacity']));
      $order->setClosed($data['orderClosed']);
      $em->persist($order);
      $em->flush();
    }

    return $this->redirect($this->generateUrl("app_adminpizza_creneau"));
  }

  /**
   * @Route("/creneau/{id}/remove")
   * @Method({"POST"})
   */
  public function creneau_removeAction(Entity\PizzaOrder $order) {

      try {
        $em = $this->getDoctrine()->getManager();
        $em->remove($order);
        $em->flush();
      } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException  $e) {
        $this->get('session')->getFlashBag()->add('error', "Suppression impossible : des commandes sont associées à ce créneau.");
      }

      return $this->redirect($this->generateUrl("app_adminpizza_creneau"));
  }

    /**
     * @Route("/commande")
     * @Route("/commande/{id}")
     * @Template()
     */
    public function commandeAction(Request $request, $id = null) {
        $em = $this->getDoctrine()->getManager();
        $order = $formAdd = null;

        $showAll = $request->query->get("showAll", false);

        $orders = $em->getRepository('App\Entity\PizzaOrder')->getAll();
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
                  'choices' => $ordersChoices))
            ->setAction($this->generateUrl('app_adminpizza_commande', ['showAll' => $showAll]))
            ->getForm();

        $form->handleRequest($request);

        if ($form-> isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            return $this->redirect($this->generateUrl(
                'app_adminpizza_commande_1',
                array('id' => $data['order'], 'showAll' => $showAll)));
        }

        if($id) {

            $order = $em->getRepository('App\Entity\PizzaOrder')->getOneById($id);

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
     * @Route("/{id}/add")
     * @Method({"POST"})
     */
    public function addAction(Entity\PizzaOrder $order, Request $request) {

        $em = $this->getDoctrine()->getManager();

        $form = $this->getAddUserOrderForm($order);
        $form->handleRequest($request);

        if ($form-> isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $uo = new Entity\PizzaUserOrder();
            $uo->setUsernameCanonical($data['username']);
            $uo->setFullnameCanonical($data['fullname']);
            $uo->setPizza($em->getRepository("App\Entity\Pizza")->findOneById($data['pizza']));
            $uo->setOrder($order);
            $uo->setPaymentDone(true);
            $uo->setType(Entity\PizzaUserOrder::TYPE_MANUAL);
            $uo->setPrice($data['price']);

            $user = $em->getRepository("App\Entity\User")->findOneByUsername($data['username']);
            if($user)
                $uo->setUser($user);

            $em->persist($uo);
            $em->flush();
        }

        return $this->redirect($this->generateUrl("app_adminpizza_commande_1", array("id" => $order->getId())));

    }

    /**
     * @Route("/{id}/lock")
     * @Method({"POST"})
     */
    public function lockAction(Entity\PizzaOrder $order) {
        $em = $this->getDoctrine()->getManager();
        $order->setClosed(true);
        $em->persist($order);
        $em->flush();
        return $this->redirect($this->generateUrl("app_adminpizza_commande_1", array("id" => $order->getId())));
    }

    /**
     * @Route("/{id}/unlock")
     * @Method({"POST"})
     */
    public function unlockAction(Entity\PizzaOrder $order) {
        $em = $this->getDoctrine()->getManager();
        $order->setClosed(false);
        $em->persist($order);
        $em->flush();
        return $this->redirect($this->generateUrl("app_adminpizza_commande_1", array("id" => $order->getId())));
    }

    /**
     * @Route("/order/{id}/remove")
     * @Method({"POST"})
     */
    public function order_removeAction(Entity\PizzaUserOrder $uo) {

        $em = $this->getDoctrine()->getManager();

        $id = $uo->getOrder()->getId();

        $em->remove($uo);
        $em->flush();

        return $this->redirect($this->generateUrl("app_adminpizza_commande_1", array("id" => $id)));
    }

    /**
     * @Route("/order/{id}/status/{status}")
     * @Method({"POST"})
     */
    public function order_statusAction(Entity\PizzaUserOrder $uo, $status) {

        $em = $this->getDoctrine()->getManager();

        $status = intval($status);

        $uo->setDelivered($status === 1);
        $em->persist($uo);
        $em->flush();

        return new JsonResponse(array("err" => null));

    }

    /////////////// PRIVATE //////////

    private function getAddUserOrderForm(Entity\PizzaOrder $o) {

        $em = $this->getDoctrine()->getManager();

        $pizzas = $em->getRepository('App\Entity\Pizza')->findAll();
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
                        'choices' => $pizzasChoices,
                        'label' => 'Pizza'))
                    ->add('price', ChoiceType::class, array(
                        'choices' =>
                            array(
                                'Joueur' => Entity\PizzaUserOrder::FULL_PRICE,
                                'Staff' => Entity\PizzaUserOrder::STAFF_PRICE,
                                'Gratuit' => Entity\PizzaUserOrder::FREE_PRICE
                            ),
                        'label' => 'Tarif'))
                    ->setAction($this->generateUrl('app_adminpizza_add', array('id' => $o->getId())))
                    ->getForm();
    }

    private function getAddPizzaForm() {
        return $this->createFormBuilder()
                    ->add('pizzaName', TextType::class, array('label' => 'Nom', 'required' => true))
                    ->add('pizzaPrice', NumberType::class, array('label' => 'Prix', 'required' => true))
                    ->add('pizzaVeggie', CheckboxType::class, array('label' => 'Veggie', 'required' => false))
                    ->add('pizzaDescription', TextType::class, array('label' => 'Description', 'required' => false))
                    ->setAction($this->generateUrl('app_adminpizza_pizza_add'))
                    ->getForm();
    }

    private function getAddOrderForm() {
        return $this->createFormBuilder()
                    ->add('orderExpirationDateTime', DateTimeType::class, array('required' => true))
                    ->add('orderDeliveryDateTime', DateTimeType::class, array('required' => true))
                    ->add('orderCapacity', NumberType::class, array('label' => 20, 'required' => false))
                    ->add('orderForeignCapacity', NumberType::class, array('label' => 3, 'required' => false))
                    ->add('orderClosed', CheckboxType::class, array('required' => false))
                    ->setAction($this->generateUrl('app_adminpizza_creneau_add'))
                    ->getForm();
    }

}
