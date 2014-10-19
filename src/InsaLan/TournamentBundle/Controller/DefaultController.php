<?php

namespace InsaLan\TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Doctrine\ORM\Query;

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
        //$this->get('session')->getFlashBag()->add('info', 'Hey !');

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
     * @Route("/teams")
     * @Template()
     */
    public function teamListAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery("
            SELECT partial t.{id,name,validated}, partial p.{id,name,lolId} 
            FROM InsaLanTournamentBundle:Team t
            JOIN t.players p
            ");
        $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
        $query->execute();

        return array('teams' => $query->getResult());
    }
}
