<?php

namespace InsaLan\InformationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use InsaLan\InsaLanBundle\Entity;
use InsaLan\TournamentBundle\Entity\Participant;
use InsaLan\InformationBundle\Entity\LegalDocument;

class DefaultController extends Controller
{
    const OPENING_DATE = '2018/12/24 20:00:00';

    /**
     * @Route("/asso")
     * @Template()
     */
    public function assoAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Get global variables
        $globalVars = array();
        $globalKeys = ['staffNumber', 'playersNumber',
                      'number', 'lettersNumber'];
        $globalVars = $em->getRepository('InsaLanBundle:GlobalVars')->getGlobalVars($globalKeys);

        return array('globalVars' => $globalVars);
    }

    /**
     * @Route("/public")
     * @Template()
     */
    public function publicAction()
    {
        return array();
    }

    /**
     * @Route("/tournament")
     * @Template()
     */
    public function tournamentAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Get global variables
        $globalVars = array();
        $globalKeys = ['playersNumber'];
        $globalVars = $em->getRepository('InsaLanBundle:GlobalVars')->getGlobalVars($globalKeys);

        return array('globalVars' => $globalVars);
    }

    /**
     * @Route("/wwwh")
     * @Template()
     */
    public function wwwhAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Get global variables
        $globalVars = array();
        $globalKeys = ['staffNumber', 'number', 'lettersNumber',
                      'playersNumber', 'openingDate', 'openingHour', 'closingDate', 'closingHour', 'price', 'webPrice'];
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
     * @Route("/cosplay/rules")
     * @Template()
     */
    public function cosplayRulesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $rules = $em->getRepository('InsaLanInformationBundle:LegalDocument')->findOneByShortName("cosplayrules");
        return array("rules" => $rules);
    }
    
    /**
     * @Route("/baston")
     * @Template()
     */
    public function bastonAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Get global variables
        $globalVars = array();
        $globalKeys = ['bastonRegisterLink', 'bastonStartRegistration'];

        $globalVars = $em->getRepository('InsaLanBundle:GlobalVars')->getGlobalVars($globalKeys);

        return array('globalVars' => $globalVars);
    }

    /**
     * @Route("/playersinfos")
     * @Template()
     *//*
    public function playersInfosAction()
    {
        return array();
    }*/

    /**
     * @Route("/salesterms")
     * @Template()
     */
    public function salesTermsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $salesTerms = $em->getRepository('InsaLanInformationBundle:LegalDocument')->findOneByShortName("cgv");
        return array("salesterms" => $salesTerms);
    }

    /**
     * @Route("/mentions")
     * @Template()
     */
    public function mentionsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $mentions = $em->getRepository('InsaLanInformationBundle:LegalDocument')->findOneByShortName("mentions");
        return array("mentions" => $mentions);
    }

    /**
     * @Route("/hardwareRental")
     * @Template()
     */
    public function hardwareRentalAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Get global variables
        $globalVars = array();
        // Safety  valve
        $globalKeys = ['rentalPageEnabled'];

        $globalVars = $em->getRepository('InsaLanBundle:GlobalVars')->getGlobalVars($globalKeys);

        // Disallow access until date reached
        if(time() < strtotime(self::OPENING_DATE) || $globalVars['rentalPageEnabled'] != "True") {
            $this->get('session')->getFlashBag()->add('error', "Page indisponible pour le moment. Revenez plus tard !");
            return $this->redirect($this->generateUrl(
                'insalan_news_default_index'));
            }

        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $participants = $em->getRepository('InsaLanTournamentBundle:Participant')->findByUser($usr);

        // Check if player's team / player / manager is validated
        $validated = False;
        foreach($participants as $p) {
            if ($p->getValidated() == Participant::STATUS_VALIDATED && !$p->getTournament()->isClosed()) {
                $validated = True;
            }
        }
        return array('validated' => $validated);
    }
}
