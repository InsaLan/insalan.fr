<?php

namespace InsaLan\TicketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use InsaLan\TournamentBundle\Entity\Player;
use InsaLan\TournamentBundle\Entity\Manager;
use InsaLan\TournamentBundle\Entity\Tournament;
use InsaLan\TournamentBundle\Entity\Participant;

class AdminController extends Controller
{
    /**
     * @Route("/admin")
     * @Template()
     */
    public function indexAction()
    {
      $em = $this->getDoctrine()->getManager();
      $tournaments = $em->getRepository('InsaLanTournamentBundle:Tournament')->getUpcomingTournaments();
      $soloTournaments = $teamTournaments = array();
      foreach ($tournaments as $tournament) {
        if ($tournament->getParticipantType() == 'team') {
          $teamTournaments[] = $tournament;
        } else if ($tournament->getParticipantType() == 'player'){
          $soloTournaments[] = $tournament;
        }
      }
      $soloPlayers = $em->getRepository('InsaLanTournamentBundle:Player')->findBy(["tournament" => $soloTournaments, "validated" => Participant::STATUS_VALIDATED]);
      $teamPlayers = $em->getRepository('InsaLanTournamentBundle:Player')->getValidatedTeamPlayers($teamTournaments);
      $players = $soloPlayers + $teamPlayers;

      // get validated managers
      $managers = array();
      $teams = $em->getRepository('InsaLanTournamentBundle:Team')->findBy(["tournament" => $teamTournaments, "validated" => Participant::STATUS_VALIDATED]);
      foreach ($soloPlayers as $player) {
        if ($player->getManager() && $player->getManager()->isOk()) {
          $managers[] = $player->getManager();
        }
      }
      foreach ($teams as $team) {
        if ($team->getManager() && $team->getManager()->isOk()) {
          $managers[] = $team->getManager();
        }
      }
      return array("players" => $players, "managers" => $managers);
    }
}
