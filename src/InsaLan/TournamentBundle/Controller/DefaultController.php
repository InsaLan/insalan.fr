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
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tournaments = $em->getRepository('InsaLanTournamentBundle:Tournament')->findAll();
        return array('tournaments' => $tournaments);
    }

    /**
     * @Route("/{id}", requirements={"id" = "\d+"})
     * @Template()
     */
    public function tournamentAction(Entity\Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $stages = $em->getRepository('InsaLanTournamentBundle:GroupStage')->findByTournament($tournament);

        foreach ($stages as $s) {
            foreach ($s->getGroups() as $g) {
                $g->countWins();
            }
        }

        return array('t' => $tournament, 'stages' => $stages);
    }

    /**
     * @Route("/team")
     * @Template()
     */
    public function teamListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $teams =  $em->getRepository('InsaLanTournamentBundle:Team')->getAllTeams();

        return array('teams' => $teams);
    }

    /**
     * @Route("/team/captain")
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
     * @Route("/team/{id}", requirements={"id" = "\d+"})
     * @Template()
     */
    public function teamDetailsAction(Entity\Team $team)
    {   
        $pvpService = $this->get('insalan.tournament.pvp_net');

        foreach ($team->getGroups() as $g)
        {
            $g->countWins();
            // TODO : LoL Only !
            foreach ($g->getMatches() as $m)
            {   
                $name = "INSALAN Match " . $m->getId();
                $m->pvpNetUrl = $pvpService->generateUrl(array("name" => $name));
            }
        }

        return array("team" => $team, "authorized" => $this->isUserInTeam($team));
    }

    /**
     * @Route("/team/{id}/validate/{match}", requirements={"id" = "\d+"})
     * @Template()
     */
    public function teamValidateMatchAction(Entity\Team $team, Entity\Match $match)
    {
        $pvpService = $this->get('insalan.tournament.pvp_net');

        if($match->getPart1() !== $team && $match->getPart2() !== $team)
            throw new \Exception("Invalid team");

        if(!$this->isUserInTeam($team))
            throw new \Exception("Invalid user");

        if($match->getState() != Entity\Match::STATE_ONGOING)
            throw new \Exception("Invalid match : not in ongoing state");

        $matchResult = $pvpService->getGameResult($match->getPart1(), $match->getPart2());

        $round = new Entity\Round();
        $round->setMatch($match);

        $round->setScore1(0);
        $round->setScore2(0);

        if($matchResult)
            $round->setScore1(1);

        else
            $round->setScore2(1);

        // TODO : not for LoL only
        
        $match->setState(Entity\Match::STATE_FINISHED);

        $em = $this->getDoctrine()->getManager();
        $em->persist($round);
        $em->persist($match);
        $em->flush();

        return $this->redirect($this->generateUrl('insalan_tournament_default_teamdetails', array('id' => $team->getId())));

    }

    /**
     * @Route("/team/{id}/addReplay/{round}", requirements={"id" = "\d+"})
     * @Template()
     */
    public function roundAddReplayAction(Request $request, Entity\Team $team, Entity\Round $round)
    {   

        // Check security
        if(!$this->isUserInTeam($team))
            throw new \Exception("Invalid user");

        if($round->getMatch()->getPart1()->getId() !== $team->getId()
        && $round->getMatch()->getPart2()->getId() !== $team->getId())
            throw new \Exception("Invalid round");

        if($round->getReplay() !== null)
            throw new \Exception("Le fichier a déjà été envoyé !");

        $form = $this->createFormBuilder($round)
            ->add('replayFile', 'file', array("label" => "Fichier"))
            ->add('save', 'submit', array("label" => "Ajouter le fichier"))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid())
        {   
            $em = $this->getDoctrine()->getManager();
            $em->persist($round);
            $em->flush();

            return $this->redirect($this->generateUrl('insalan_tournament_default_teamdetails', array('id' => $team->getId())));
        }

        return array("form" => $form->createView());
    }

    /** PRIVATE **/

    private function isUserInTeam(Entity\Team $team)
    {   

        $user = $this->get('security.context')->getToken()->getUser();

        foreach ($team->getPlayers() as $p)
        {
            if($p->getUser()->getId() === $user->getId())
            {
                return true;
            }

        }

        return false;
    }

}
