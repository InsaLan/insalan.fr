<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Entity\Tournament;

/**
 * @Route("/stream")
 */
class StreamController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tournaments = $em->getRepository('App\Entity\Tournament')->findPlaying();
        $streams = $em->getRepository('App\Entity\Stream')->findBy(array('tournament' => $tournaments, 'display' => true));
        $officialStreams = array();
        $unofficialStreams = array();
          foreach ($streams as $s) {
            if ($s->getOfficial()) {
              $officialStreams[] = $s;
            } else {
              $unofficialStreams[] = $s;
            }
          }

        // Get global variables
        $globalVars = array();
        $globalKeys = ['topStream'];
        $globalVars = $em->getRepository('App\Entity\InsaLanGlobalVars')->getGlobalVars($globalKeys);

        return array('tournaments' => $tournaments, 'officialStreams' => $officialStreams, 'unofficialStreams' => $unofficialStreams, 'globalVars' => $globalVars);
    }
}
