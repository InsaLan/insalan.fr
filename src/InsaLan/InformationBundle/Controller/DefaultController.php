<?php

namespace InsaLan\InformationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    const OPENING_DATE = '2014/10/03 00:00:00';

    /**
     * @Route("/faq")
     * @Template()
     */
    public function faqAction()
    {
        return array();
    }

    /**
     * @Route("/cosplay")
     * @Template()
     */
    public function cosplayAction()
    {
        return array();
    }
}
