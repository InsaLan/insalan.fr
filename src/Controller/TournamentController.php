<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

use App\Entity\Tournament;
use App\Entity\TournamentTeam;
use App\Entity\Participant;


/**
 * @Route("/tournament")
 */
class TournamentController extends Controller
{
    /**
     * Public page indexing all tounaments
     * @Route("/public")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // TODO: Manage tournaments with yearview
        $tournaments = $em->getRepository('App\Entity\Tournament')->findThisYearTournaments(15);

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
        return $this->render('tournamentIndex.html.twig',['old_tournaments' => $old_tournaments,
        'registration_closed_tournaments' => $registration_closed_tournaments,
         'future_tournaments' => $future_tournaments,
        'opened_tournaments' => $opened_tournaments]);

    }

    /**
     * Display tournament's match tree
     * @Route("/{id}/public", requirements={"id" = "\d+"})
     * @Entity("Tournament", expr="App\Repository\TournamentRepository.find(id)")
     * @Template()
     */
    public function tournamentAction(Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();

        $stages = $em->getRepository('App\Entity\TournamentGroupStage')->getByTournament($tournament);
        $KOs = $em->getRepository('App\Entity\TournamentKnockout')->getByTournament($tournament);

        foreach ($stages as $s) {
            foreach ($s->getGroups() as $g) {
                $g->countWins();
            }
        }

        $output = array();

        foreach($KOs as $ko) {
            $ko->jsonData = $em->getRepository('App\Entity\TournamentKnockoutMatch')->getJson($ko);
            $output[] = $ko;
        }
        return array('t' => $tournament, 'stages' => $stages, 'knockouts' => $output);
//        return $this->render('tournamentStages.html.twig',['t' => $tournament, 'stages' => $stages, 'knockouts' => $output]);
    }

    /**
     * Display tournament's rules
     * @Route("/{id}/public/rules", requirements={"id" = "\d+"})
     * @Template()
     */
    public function rulesAction(Tournament $tournament)
    {
        return $this->render('tournamentRules.html.twig',['t' => $tournament]);
    }

    /**
     * Display the teams currently registered or pending for this tournament
     * @Route("/{id}/public/team", requirements={"id" = "\d+"})
     * @Template()
     */
    public function teamListAction(Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $teams =  $em->getRepository('App\Entity\TournamentTeam')->getTeamsForTournament($tournament);

        $nbPendingTeams = $nbWaitingTeams = 0;

        foreach($teams as $t) {
            if ($t->getValidated() == Participant::STATUS_PENDING) $nbPendingTeams++;
            if ($t->getValidated() == Participant::STATUS_WAITING) $nbWaitingTeams++;
        }
        return $this->render('tournamentTeamList.html.twig',['teams' => $teams, 'tournament' => $tournament, 'nbPendingTeams' => $nbPendingTeams, 'nbWaitingTeams' => $nbWaitingTeams]);
    }

    /**
     * Display the players currently registered or pending for this tournament
     * @Route("/{id}/public/player", requirements={"id" = "\d+"})
     * @Template()
     */
    public function playerListAction(Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $players =  $em->getRepository('App\Entity\Player')->getPlayersForTournament($tournament);

        $nbPendingPlayers = $nbWaitingPlayers = 0;

        foreach($players as $p) {
            if ($p->getValidated() == Participant::STATUS_PENDING) $nbPendingPlayers++;
            if ($p->getValidated() == Participant::STATUS_WAITING) $nbWaitingPlayers++;
        }
        return $this->render('tournamentPlayerList.html.twig', ['players' => $players, 'tournament' => $tournament, 'nbPendingPlayers' => $nbPendingPlayers, 'nbWaitingPlayers' => $nbWaitingPlayers]);
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
        $team = $em->getRepository('App\Entity\TournamentTeam')->find($team_id);

        if (!$team) {
            throw $this->createNotFoundException(
                'No team found for this id : '.$team_id
            );
        }

        foreach($team->getPlayers() as $player) {
            if ($player->getId() == $player_id) {
                $team->setCaptain($player);
                $em->flush();
                return $this->redirect($this->generateUrl('app_user_index'));
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

        $match = $em->getRepository('App\Entity\TournamentMatch')->getById($id);
        $group = $em->getRepository('App\Entity\TournamentGroup')->getById($match->getGroup()->getId(), false);
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
            $r['score'] = array($round->getScore($match->getPart1()), $round->getScore($match->getPart2()));
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
    public function knockoutAction(Tournament $t)
    {
        $em = $this->getDoctrine()->getManager();

        $KOs = $em->getRepository('App\Entity\TournamentKnockout')->findByTournament($t);

        $output = array();

        foreach($KOs as $ko) {
            $ko->jsonData = $em->getRepository('App\Entity\TournamentKnockoutMatch')->getJson($ko);
            $output[] = $ko;
        }
        return $this->render('tournamentKnockout.html.twig',["tournament" => $t, "knockouts" => $output]);


    }

    /**
     * Get match details (used in pool view)
     * @Route("/public/team/{id}", requirements={"id" = "\d+"})
     * @Template()
     */
    public function teamDetailsAction(Participant $part)
    {

        // Get Knockout & Group Matches

        $em = $this->getDoctrine()->getManager();
        $simpleMatches = $em->getRepository("App\Entity\TournamentMatch")->getByParticipant($part);
        $royalMatches = $em->getRepository("App\Entity\TournamentRoyalMatch")->getByParticipant($part);

        $matches = array_merge($simpleMatches, $royalMatches);

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
        return $this->render('tournamentTeamDetails.html.twig',["part" => $part, "groupMatches" => $grs, "knockoutMatches" => $kos, "authorized" => $this->isUserInTeam($part)]);
    }

    private function isUserInTeam(Participant $part) {

        $user = $this->get('security.token_storage')->getToken()->getUser();
        if(!$user || $user === "anon.") return false;

        if($part instanceof TournamentTeam) {

            foreach ($part->getPlayers() as $p) {
                if($p->getUser() && $p->getUser()->getId() === $user->getId())
                    return true;
            }
            return false;
        }

        return $part->getUser() === $user;

    }
}
