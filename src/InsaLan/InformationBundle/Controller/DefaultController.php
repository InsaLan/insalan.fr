<?php

namespace InsaLan\InformationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    const OPENING_DATE = '2014/10/03 00:00:00';

    /**
     * @Route("/")
     * @Template()
     */
    public function homeAction()
    {
        if(time() < strtotime(self::OPENING_DATE)) {
            return $this->redirect($this->generateUrl(
                'insalan_information_default_wait'));
        }

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
     * @Route("/faq-normal")
     * @Template()
     */
    public function indexAction()
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

    /**
     * @Route("/wait")
     * @Template()
     */
    public function waitAction()
    {
        if(time() >= strtotime(self::OPENING_DATE)) {
            return $this->redirect($this->generateUrl('insalan_information_default_home'));
        }
        return array();
    }
}
