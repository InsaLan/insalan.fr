<?php

namespace InsaLan\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use InsaLan\PizzaBundle\Entity;
use InsaLan\ApiBundle\Http\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/tournament")
     * @Template()
     */
    public function tournamentAction()
    {
        return array();
    }

    /**
     * @Route("/pizza")
     * @Route("/pizza/{id}")
     * @Template()
     */
    public function pizzaAction($id = null) {
      return $this->redirect($this->generateUrl("insalan_pizza_admin_index", array("id" => $id)));
    }

    /**
     * @Route("/web")
     * @Template()
     */
    public function webAction()
    {
        return array();
    }
}
