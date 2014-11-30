<?php

namespace InsaLan\TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use InsaLan\TournamentBundle\Entity;

class DefaultController extends Controller
{
    /**
     * @Route("/public")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tournaments = $em->getRepository('InsaLanTournamentBundle:Tournament')->findAll();
        return array('tournaments' => $tournaments);
    }

    /**
     * @Route("/public/{id}", requirements={"id" = "\d+"})
     * @Template()
     */
    public function tournamentAction(Entity\Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $stages = $em->getRepository('InsaLanTournamentBundle:GroupStage')->getByTournament($tournament);
        $knockout = $em->getRepository('InsaLanTournamentBundle:Knockout')->findByTournament($tournament);

        foreach ($stages as $s) {
            foreach ($s->getGroups() as $g) {
                $g->countWins();
            }
        }

        return array('t' => $tournament, 'stages' => $stages, 'knockout' => $knockout);
    }

    /**
     * @Route("/public/team")
     * @Template()
     */
    public function teamListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $teams =  $em->getRepository('InsaLanTournamentBundle:Team')->getAllTeams();

        return array('teams' => $teams);
    }

    /**
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
     * @Route("/public/team/{id}")
     * @Method({"GET"})
     * @Template
     */
    public function teamPageAction($id) {
        $em = $this->getDoctrine()->getManager();
        $team = $em->getRepository('InsaLanTournamentBundle:Team')->findOneById($id);
        if ($team === null) {
            return $this->createNotFoundException('Cette équipe est introuvable');
        }
        return array('team' => $team);
    }

    /**
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
}
