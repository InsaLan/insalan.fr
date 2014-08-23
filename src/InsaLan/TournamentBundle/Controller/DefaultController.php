<?php

namespace InsaLan\TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
        return array('t' => $tournament);
    }

    /**
     * @Template()
     */
    public function stagesAction(Entity\Tournament $tournament)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('InsaLanTournamentBundle:GroupStage');

        $stages = $repo->getByTournament($tournament);
        return array('stages' => $stages);
    }

    /**
     * @Template()
     */
    public function groupsAction(Entity\GroupStage $stage)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('InsaLanTournamentBundle:Group');

        $groups = $repo->getByStage($stage);
        foreach ($groups as &$group) {
            $group->countWins();
        }

        return array('groups' => $groups);
    }
}
