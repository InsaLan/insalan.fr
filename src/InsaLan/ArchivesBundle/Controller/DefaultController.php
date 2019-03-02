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
  * @Route("/{edition}", requirements={"edition" = "\d+"})
  * @Template()
  */
  public function previousYearAction(\InsaLan\ArchivesBundle\Entity\Edition $edition)
  {
    $em = $this->getDoctrine()->getManager();

    $old_tournaments = $em->getRepository('InsaLanTournamentBundle:Tournament')->findPreviousYearTournaments($edition->getYear());
    $picturesAlbum = $em->getRepository('InsaLanArchivesBundle:PictureAlbum')->findByEdition($edition);
    $streamsAlbum = $em->getRepository('InsaLanArchivesBundle:Stream')->findStreamsAlbumByEdition($edition);

    $output = array(
      'edition' => $edition,
      'old_tournaments' => $old_tournaments,
      'streamsAlbum' => $streamsAlbum,
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
  * @Route("/{edition}/streams/{album}/{page}", requirements={"edition" = "\d+", "page" = "\d+"}, name="archives_streams")
  * @Template()
  */
  public function previousYearStreamsAction(\InsaLan\ArchivesBundle\Entity\Edition $edition, $album, $page)
  {
    $em = $this->getDoctrine()->getManager();

    $streams = $em->getRepository('InsaLanArchivesBundle:Stream')->findBy(array ('edition' => $edition, 'album' => $album));
    return array('edition' => $edition, 'streams' => $streams, 'album' => $album, 'page' => $page);
  }
}
