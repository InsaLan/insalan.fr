<?php

namespace InsaLan\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
     * @Template()
     */
    public function pizzaAction()
    {
        return array();
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
