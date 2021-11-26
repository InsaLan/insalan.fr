<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/archives")
 */
class ArchivesController extends Controller
{
  /**
  * @Route("/")
  * @Template()
  */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();
    $editions = $em->getRepository('App\Entity\ArchivesEdition')->getEditions();

    return array('editions' => $editions);
  }

  /**
  * @Route("/{edition}", requirements={"edition" = "\d+"})
  * @Template()
  */
  public function previousYearAction(\App\Entity\ArchivesEdition $edition)
  {
    $em = $this->getDoctrine()->getManager();

    $old_tournaments = $em->getRepository('App\Entity\Tournament')->findPreviousYearTournaments($edition->getYear());
    $picturesAlbum = $em->getRepository('App\Entity\ArchivesPictureAlbum')->findByEdition($edition);
    $streamsAlbum = $em->getRepository('App\Entity\ArchivesStream')->findStreamsAlbumByEdition($edition);

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
  public function previousYearStreamsAction(\App\Entity\ArchivesEdition $edition, $album, $page)
  {
    $em = $this->getDoctrine()->getManager();

    $streams = $em->getRepository('App\Entity\ArchivesStream')->findBy(array ('edition' => $edition, 'album' => $album));
    return array('edition' => $edition, 'streams' => $streams, 'album' => $album, 'page' => $page);
  }
}
