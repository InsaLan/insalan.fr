<?php

namespace InsaLan\StreamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use InsaLan\TournamentBundle\Entity\Tournament;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tournaments = $em->getRepository('InsaLanTournamentBundle:Tournament')->findPlaying();
        $streams = $em->getRepository('InsaLanStreamBundle:Stream')->findByTournament($tournaments);
        $officialStreams = array();
        $unofficialStreams = array();
          foreach ($streams as $s) {
            if ($s->getOfficial()) {
              $officialStreams[] = $s;
            } else {
              $unofficialStreams[] = $s;
            }
          }

        return array('tournaments' => $tournaments, 'officialStreams' => $officialStreams, 'unofficialStreams' => $unofficialStreams);
    }
}
