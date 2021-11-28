<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Entity;
use App\Entity\Participant;
use App\Entity\InformationLegalDocument;

/**
 * @Route("/infos")
 */
class InformationController extends Controller
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
        $globalVars = $em->getRepository('App\Entity\InsaLanGlobalVars')->getGlobalVars($globalKeys);
        return $this->render('informationAsso.html.twig', ['globalVars' => $globalVars]);
    }

    /**
     * @Route("/public")
     * @Template()
     */
    public function publicAction()
    {
        return $this->render('informationPublic.html.twig');
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
        $globalVars = $em->getRepository('App\Entity\InsaLanGlobalVars')->getGlobalVars($globalKeys);
        return $this->render('informationTournament.html.twig', ['globalVars' => $globalVars]);
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
        $globalVars = $em->getRepository('App\Entity\InsaLanGlobalVars')->getGlobalVars($globalKeys);

        // Get staff
        $staff = $em->getRepository('App\Entity\InsaLanStaff')->findAll();
        return $this->render('informationWwwh.html.twig', ['globalVars' => $globalVars, 'staff' => $staff]);
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

        $globalVars = $em->getRepository('App\Entity\InsaLanGlobalVars')->getGlobalVars($globalKeys);
        return $this->render('informationCosplay.html.twig', ['globalVars' => $globalVars]);
    }

    /**
     * @Route("/cosplay/rules")
     * @Template("@InsaLanInformationBundle/Resources/views/Default/legalDocument.html.twig")
     */
    public function cosplayRulesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $document = $em->getRepository('App\Entity\InformationLegalDocument')->findOneByShortName("cosplayrules");
        return $this->render('informationLegalDocument.html.twig', ['document' => $document]);
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

        $globalVars = $em->getRepository('App\Entity\InsaLanGlobalVars')->getGlobalVars($globalKeys);
        return $this->render('informationBaston.html.twig', ['globalVars' => $globalVars]);
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
     * @Template("@InsaLanInformationBundle/Resources/views/Default/legalDocument.html.twig")
     */
    public function salesTermsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $document = $em->getRepository('App\Entity\InformationLegalDocument')->findOneByShortName("cgv");
        return $this->render('informationLegalDocument.html.twig', ['document' => $document]);
    }

    /**
     * @Route("/mentions")
     * @Template("@InsaLanInformationBundle/Resources/views/Default/legalDocument.html.twig")
     */
    public function mentionsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $document = $em->getRepository('App\Entity\InformationLegalDocument')->findOneByShortName("mentions");
        return $this->render('informationLegalDocument.html.twig', ['document' => $document]);
    }

    /**
     * @Route("/cookiespolicy")
     * @Template("@InsaLanInformationBundle/Resources/views/Default/legalDocument.html.twig")
     */
    public function cookiespolicyAction()
    {
        $em = $this->getDoctrine()->getManager();
        $document = $em->getRepository('App\Entity\InformationLegalDocument')->findOneByShortName("cookiespolicy");
        return $this->render('informationLegalDocument.html.twig', ['document' => $document]);
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

        $globalVars = $em->getRepository('App\Entity\InsaLanGlobalVars')->getGlobalVars($globalKeys);

        // Disallow access until date reached
        if(time() < strtotime(self::OPENING_DATE) || $globalVars['rentalPageEnabled'] != "True") {
            $this->get('session')->getFlashBag()->add('error', "Page indisponible pour le moment. Revenez plus tard !");
            return $this->redirect($this->generateUrl(
                'index'));
            }

        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $participants = $em->getRepository('App\Entity\Participant')->findByUser($usr);

        // Check if player's team / player / manager is validated
        $validated = False;
        foreach($participants as $p) {
            if ($p->getValidated() == Participant::STATUS_VALIDATED && !$p->getTournament()->isClosed()) {
                $validated = True;
            }
        }
        return $this->render('informationHardwareRental.html.twig', ['validated' => $validated]);
    }
}
