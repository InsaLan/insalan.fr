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
    public function previousYearAction($year)
    {
        $em = $this->getDoctrine()->getManager();

        $old_tournaments = $em->getRepository('InsaLanTournamentBundle:Tournament')->findPreviousYearTournaments($year);
        $pictures = $em->getRepository('InsaLanArchivesBundle:Picture')->findPreviousYearPictures($year);
        $streams = $em->getRepository('InsaLanArchivesBundle:Stream')->findPreviousYearStreams($year);
        $edition = $em->getRepository('InsaLanArchivesBundle:Edition')->findOneByYear($year);

        $output = array(
          'old_tournaments' => $old_tournaments,
          'pictures' => $pictures,
          'streams' => $streams,
           'year' => $year
         );
        if ($edition !=  null && $edition->getTrailerAvailable() ) {
          $trailer = $edition->getTrailerUrl();
          $output['trailer'] = $trailer;
        } elseif ($edition !=  null) {
          $poster = $edition->getImage();
          $output['poster'] = $poster;
        }
        return $output;
    }
}
