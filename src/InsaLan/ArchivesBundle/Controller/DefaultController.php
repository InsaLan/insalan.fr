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
    $streamsAlbum = $em->getRepository('InsaLanArchivesBundle:Stream')->findPreviousYearStreamsAlbum($year);
    $edition = $em->getRepository('InsaLanArchivesBundle:Edition')->findOneByYear($year);
    $picturesAlbum = $em->getRepository('InsaLanArchivesBundle:Picture')->findPreviousYearPicturesAlbum($year);

    $output = array(
      'old_tournaments' => $old_tournaments,
      'streamsAlbum' => $streamsAlbum,
      'year' => $year,
      'picturesAlbum' => $picturesAlbum
    );

    if ($edition !=  null && $edition->getTrailerAvailable() ) {
      $trailer = $edition->getTrailerUrl();
      $output['trailer'] = $trailer;
    } elseif ($edition !=  null) {
      $poster = $edition->getImage();
      $output['poster'] = $poster;
    }

    if ($edition !=  null && $edition->getAftermovieUrl() != null) {
      $aftermovie = $edition->getAftermovieUrl();
      $output['aftermovie'] = $aftermovie;
    }
    return $output;
  }


  /**
  * @Route("/{year}/pictures/{album}/{page}", requirements={"year" = "\d+", "page" = "\d+"}, name="archives_pictures")
  * @Template()
  */
  public function previousYearPicturesAction($year, $album, $page)
  {
    $em = $this->getDoctrine()->getManager();

    $pictures = $em->getRepository('InsaLanArchivesBundle:Picture')->findPreviousYearPicturesByAlbum($year, $album);

    return array('pictures' => $pictures, 'year' => $year, 'album' => $album, 'page' => $page);
  }


  /**
  * @Route("/{year}/streams/{album}/{page}", requirements={"year" = "\d+", "page" = "\d+"}, name="archives_streams")
  * @Template()
  */
  public function previousYearStreamsAction($year, $album, $page)
  {
    $em = $this->getDoctrine()->getManager();

    $streams = $em->getRepository('InsaLanArchivesBundle:Stream')->findPreviousYearStreamsByAlbum($year, $album);

    return array('streams' => $streams, 'year' => $year, 'album' => $album, 'page' => $page);
  }
}
