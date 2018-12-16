<?php

namespace InsaLan\InformationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use InsaLan\InsaLanBundle\Entity;

class DefaultController extends Controller
{
    const OPENING_DATE = '2014/10/03 00:00:00';

    /**
     * @Route("/faq")
     * @Template()
     */
    public function faqAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Get global variables
        $globalVars = array();
        $globalKeys = ['staffNumber', 'number', 'lettersNumber',
                      'playersNumber', 'openingDate', 'openingHour', 'closingDate', 'closingHour', 'price', 'webPrice', 'campanilePrice'];
        $globalVars = $em->getRepository('InsaLanBundle:GlobalVars')->getGlobalVars($globalKeys);

        // Get staff
        $staff = $em->getRepository('InsaLanBundle:Staff')->findAll();

        return array('globalVars' => $globalVars, 'staff' => $staff);
    }

    /**
     * @Route("/cosplay")
     * @Template()
     */
    public function cosplayAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Get global variables
        $globalVars = array();
        $globalKeys = ['cosplayEdition', 'cosplayName', 'cosplayDate',
                      'cosplayEndRegistration'];

        $globalVars = $em->getRepository('InsaLanBundle:GlobalVars')->getGlobalVars($globalKeys);

        return array('globalVars' => $globalVars);
    }

    /**
     * @Route("/playersinfos")
     * @Template()
     */
    public function playersInfosAction()
    {
        return array();
    }

    /**
     * @Route("/salesterms")
     * @Template()
     */
    public function salesTermsAction()
    {
        return array();
    }
}
