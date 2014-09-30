<?php

namespace InsaLan\InformationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function homeAction()
    {   

        if(time() < 1412287201) {
            return $this->redirect($this->generateUrl('insalan_information_default_wait'));
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
        $em = $this->getDoctrine()->getManager();

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
        if(time() > 1412287201) {
            return $this->redirect($this->generateUrl('insalan_information_default_home'));
        }
        return array();
    }
}
