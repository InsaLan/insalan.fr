<?php

namespace InsaLan\ArchivesBundle\Controller;

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
        $em = $this->getDoctrine()->getManager();
        $editions = $em->getRepository('InsaLanArchivesBundle:Edition')->getEditions();    

        return array('editions' => $editions);
    }

    /**
     * @Route("/{year}", requirements={"year" = "\d+"})
     * @Template()
     */
    public function previousYearAction(int $year)
    {
        $em = $this->getDoctrine()->getManager();

        $old_tournaments = $em->getRepository('InsaLanTournamentBundle:Tournament')->findPreviousYearTournaments($year);    
        $pictures = $em->getRepository('InsaLanArchivesBundle:Picture')->findPreviousYearPictures($year);    
        return array('old_tournaments' => $old_tournaments,'pictures' => $pictures,"year" => $year);
    }
}
