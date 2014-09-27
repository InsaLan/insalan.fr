<?php

namespace InsaLan\InformationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/faq-normal")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        return array();
    }
    
    /**
     * @Route("/")
     * @Template()
     */
    public function homeAction()
    {
        return array();
    }
    
    /**
     * @Route("/faq")
     * @Template()
     */
    public function faqAction()
    {
        return array();
    }

    /**
     * @Route("/rules")
     * @Template()
     */
    public function rulesAction()
    {
        return array();
    }
}
