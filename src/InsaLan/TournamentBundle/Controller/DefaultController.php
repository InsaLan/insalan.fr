<?php

namespace InsaLan\TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use InsaLan\TournamentBundle\Entity;

/**
 * Public display for tournaments
 */
class DefaultController extends Controller
{
    /**
     * Public page indexing all tounaments
     * @Route("/public")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // TODO: Manage tournaments with yearview
        $tournaments = $em->getRepository('InsaLanTournamentBundle:Tournament')->findThisYearTournaments();

        // separate opened tournaments and old ones
        $old_tournaments = array();
        $opened_tournaments = array();
        $registration_closed_tournaments = array();
        $future_tournaments = array();

        foreach ($tournaments as $t) {
            if($t->isOpenedNow()) // I can register !
                $opened_tournaments[] = $t;
            elseif ($t->isOpenedInFuture()) // tournament is not opened yet
                $future_tournaments[] = $t;
            elseif ($t->isClosed()) // tournament is past and closed
                $old_tournaments[] = $t;
            else // tournament is not completed yet, but I cannot register
                $registration_closed_tournaments[] = $t;
        }

        return array('old_tournaments' => $old_tournaments,
                     'registration_closed_tournaments' => $registration_closed_tournaments,
                      'future_tournaments' => $future_tournaments,
                     'opened_tournaments' => $opened_tournaments);
    }

    /**
     * Display tournament's match tree
     * @Route("/{id}/public", requirements={"id" = "\d+"})
     * @Template()
     */
    public function tournamentAction(Entity\Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();

        $stages = $em->getRepository('InsaLanTournamentBundle:GroupStage')->getByTournament($tournament);
        $KOs = $em->getRepository('InsaLanTournamentBundle:Knockout')->getByTournament($tournament);

        foreach ($stages as $s) {
            foreach ($s->getGroups() as $g) {
                $g->countWins();
            }
        }

        $output = array();

        foreach($KOs as $ko) {
            $ko->jsonData = $em->getRepository('InsaLanTournamentBundle:KnockoutMatch')->getJson($ko);
            $output[] = $ko;
        }

        return array('t' => $tournament, 'stages' => $stages, 'knockouts' => $output);
    }

    /**
     * Display tournament's rules
     * @Route("/{id}/public/rules", requirements={"id" = "\d+"})
     * @Template()
     */
    public function rulesAction(Entity\Tournament $tournament)
    {
        return array('t' => $tournament);
    }

    /**
     * Display the teams currently registered or pending for this tournament
     * @Route("/{id}/public/team", requirements={"id" = "\d+"})
     * @Template()
     */
    public function teamListAction(Entity\Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $teams =  $em->getRepository('InsaLanTournamentBundle:Team')->getTeamsForTournament($tournament);

        $nbPendingTeams = $nbWaitingTeams = 0;

        foreach($teams as $t) {
            if ($t->getValidated() == Entity\Participant::STATUS_PENDING) $nbPendingTeams++;
            if ($t->getValidated() == Entity\Participant::STATUS_WAITING) $nbWaitingTeams++;
        }

        return array('teams' => $teams, 'tournament' => $tournament, 'nbPendingTeams' => $nbPendingTeams, 'nbWaitingTeams' => $nbWaitingTeams);
    }

    /**
     * Display the players currently registered or pending for this tournament
     * @Route("/{id}/public/player", requirements={"id" = "\d+"})
     * @Template()
     */
    public function playerListAction(Entity\Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $players =  $em->getRepository('InsaLanTournamentBundle:Player')->getPlayersForTournament($tournament);

        $nbPendingPlayers = $nbWaitingPlayers = $nbPayingOfflinePlayers = 0;

        foreach($players as $p) {
            if ($p->getValidated() == Entity\Participant::STATUS_PENDING) $nbPendingPlayers++;
            if ($p->getValidated() == Entity\Participant::STATUS_WAITING) $nbWaitingPlayers++;
            if ($p->getValidated() == Entity\Participant::STATUS_PAYING_OFFLINE) $nbPayingOfflinePlayers++;
        }

        return array('players' => $players, 'tournament' => $tournament, 'nbPendingPlayers' => $nbPendingPlayers, 'nbWaitingPlayers' => $nbWaitingPlayers, 'nbPayingOfflinePlayers' => $nbPayingOfflinePlayers);
    }


    /**
     * Allow to change a team capitain
     * @Route("/public/team/captain")
     * @Method({"POST"})
     */
    public function setCaptainAction(Request $request)
    {

        $team_id = $request->request->get('team_id');
        $player_id = $request->request->get('player_id');


        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('InsaLanTournamentBundle:Team')->find($team_id);

        if (!$team) {
            throw $this->createNotFoundException(
                'No team found for this id : '.$team_id
            );
        }

        foreach($team->getPlayers() as $player) {
            if ($player->getId() == $player_id) {
                $team->setCaptain($player);
                $em->flush();
                return $this->redirect($this->generateUrl('insalan_user_default_index'));
            }
        }

        throw $this->createNotFoundException(
            'No user found for this id : '.$player_id
        );

    }

    /**
     * API call : get JSON info for a specific match
     * @Route("/public/match/{id}")
     * @Method({"GET"})
     */
    public function matchDataAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $match = $em->getRepository('InsaLanTournamentBundle:Match')->getById($id);
        $group = $em->getRepository('InsaLanTournamentBundle:Group')->getById($match->getGroup()->getId(), false);
        $group->countWins();

        $scores = array_map(function($a) {
            return $a['won'] * 3 + $a['draw'] * 2 + $a['lost'];
        }, $group->stats);

        arsort($scores);
        $score1 = $scores[$match->getPart1()->getId()];
        $pos1 = array_search(array_search($score1, $scores), array_keys($scores)) + 1;
        $score2 = $scores[$match->getPart2()->getId()];
        $pos2 = array_search(array_search($score2, $scores), array_keys($scores)) + 1;

        $out = array();
        $out['match'] = array();
        $out['match']['participants'] = array();
        $out['match']['participants'][] = array('name' => $match->getPart1()->getName(),
            'rank' => $pos1.'/'.count($scores),
            'won'  => $group->stats[$match->getPart1()->getId()]['won'],
            'lost' => $group->stats[$match->getPart1()->getId()]['lost']);
        $out['match']['participants'][] = array('name' => $match->getPart2()->getName(),
            'rank' => $pos2.'/'.count($scores),
            'won'  => $group->stats[$match->getPart2()->getId()]['won'],
            'lost' => $group->stats[$match->getPart2()->getId()]['lost']);

        foreach ($match->getRounds() as $round) {
            $r = array();
            $r['replay'] = $round->getFullReplay();
            $r['score'] = array($round->getScore1(), $round->getScore2());
            $r['blob'] = json_decode($round->getData());
            $out['rounds'][] = $r;
        }

        return new \Symfony\Component\HttpFoundation\JsonResponse($out);
    }

    /**
     * Display the tournament knockout tree
     * @Route("/public/knockout/{id}")
     * @Method({"GET"})
     * @Template
     */
    public function knockoutAction(Entity\Tournament $t)
    {
        $em = $this->getDoctrine()->getManager();

        $KOs = $em->getRepository('InsaLanTournamentBundle:Knockout')->findByTournament($t);

        $output = array();

        foreach($KOs as $ko) {
            $ko->jsonData = $em->getRepository('InsaLanTournamentBundle:KnockoutMatch')->getJson($ko);
            $output[] = $ko;
        }

        return array("tournament" => $t, "knockouts" => $output);


    }

    /**
     * Get match details (used in pool view)
     * @Route("/public/team/{id}", requirements={"id" = "\d+"})
     * @Template()
     */
    public function teamDetailsAction(Entity\Participant $part)
    {

        // Get Knockout & Group Matches

        $em = $this->getDoctrine()->getManager();
        $matches = $em->getRepository("InsaLanTournamentBundle:Match")->getByParticipant($part);

        $kos = array();
        $grs = array();

        // Populate and sort arrays
        foreach($matches as $m)
        {
            if($m->getGroup() !== null) {
                $id = $m->getGroup()->getId();
                if(!isset($grs[$id])) {
                    $grs[$id] = array();
                }
                $grs[$id][] = $m;
            }
            elseif($m->getKoMatch() !== null) {
                $id = $m->getKoMatch()->getKnockout()->getId();
                if(!isset($kos[$id])) {
                    $kos[$id] = array();
                }
                $kos[$id][] = $m;
            }
        }

        return array("part" => $part, "groupMatches" => $grs, "knockoutMatches" => $kos, "authorized" => $this->isUserInTeam($part));
    }

    private function isUserInTeam(Entity\Participant $part) {

        $user = $this->get('security.context')->getToken()->getUser();
        if(!$user || $user === "anon.") return false;

        if($part instanceof Entity\Team) {

            foreach ($part->getPlayers() as $p) {
                if($p->getUser() && $p->getUser()->getId() === $user->getId())
                    return true;
            }
            return false;
        }

        return $part->getUser() === $user;

    }
}
