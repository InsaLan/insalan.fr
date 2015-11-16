<?php

namespace InsaLan\TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use InsaLan\TournamentBundle\Entity;
use InsaLan\TournamentBundle\Exception\ControllerException;

class AdminController extends Controller
{

    /**
     * Main page for tournament admins
     * @Route("/admin")
     * @Route("/{id}/admin", requirements={"id" = "\d+"})
     * @Template()
     */
    public function indexAction($id = null)
    {
        $em = $this->getDoctrine()->getManager();

        $tournaments = $em->getRepository('InsaLanTournamentBundle:Tournament')->findAll();
        $a = array(null => '');
        foreach ($tournaments as $t) {
            $a[$t->getId()] = $t->getName();
        }

        $form = $this->createFormBuilder()
            ->add('tournament', 'choice', array('label' => 'Tournoi', 'choices' => $a))
            ->setAction($this->generateUrl('insalan_tournament_admin_index'))
            ->getForm();


        $tournament = $data = $formKo = null;
        $stages = array();
        $ko = array();
        $players = array();

        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $data = $form->getData();
            return $this->redirect($this->generateUrl(
                'insalan_tournament_admin_index_1',
                array('id' => $data['tournament'])));
        }
        else if (null !== $id) {

            /** Print Tournament informations **/

            $data = array('tournament' => $id);
            $form->get('tournament')->submit($id);

            // Get Tournament object
            foreach ($tournaments as &$t) {
                if ($t->getId() == $data['tournament']) {
                    $tournament = $t;
                }
            }

            if (null === $tournament) {
                throw new NotFoundHttpException('InsaLan\\TournamentBundle\\Entity\\Tournament object not found.');;
            }


            // Find group stages and groups for this tournament
            $stages = $em->getRepository('InsaLanTournamentBundle:GroupStage')
                         ->findByTournament($tournament);

            // Find knockout for this tournament
            $ko = $em->getRepository('InsaLanTournamentBundle:Knockout')
                     ->findByTournament($tournament);

            $players = $em->getRepository('InsaLanTournamentBundle:Player')
                          ->getAllPlayersForTournament($tournament);


            foreach ($stages as $s) {
                foreach ($s->getGroups() as $g) {
                    $g->countWins();
                }
            }

            $formKo = $this->getFormKo($data['tournament']);

        }


        $output = array(
            'form'       => $form->createView(),
            'tournament' => $tournament,
            'stages'     => $stages,
            'knockouts'  => $ko,
            'players'    => $players
        );

        if($formKo) {
            $output['formKo'] = $formKo->createView();
        }

        return $output;
    }

    /**
     * @Route("/{t}/admin/player/{p}/tooglePayment")
     */
    public function player_tooglePaymentAction(Entity\Tournament $t, Entity\Player $p)
    {
        $em = $this->getDoctrine()->getManager();
        $p->setPaymentDone(!$p->getPaymentDone());
        $em->persist($p);
        $em->flush();

       return $this->redirect($this->generateUrl(
                'insalan_tournament_admin_index_1',
                array('id' => $t->getId())));
    }

    /**
     * @Route("/{t}/admin/player/{p}/toogleArrived")
     */
    public function player_toogleArrivedAction(Entity\Tournament $t, Entity\Player $p)
    {
        $em = $this->getDoctrine()->getManager();
        $p->setArrived(!$p->getArrived());
        $em->persist($p);
        $em->flush();

       return $this->redirect($this->generateUrl(
                'insalan_tournament_admin_index_1',
                array('id' => $t->getId())));
    }


    /**
     * @Route("/admin/stage/{id}")
     * @Template()
     */
    public function stageAction(Entity\GroupStage $g)
    {
        $em = $this->getDoctrine()->getManager();
        $matches = $em->getRepository("InsaLanTournamentBundle:Match")->getByGroupStage($g);

        return array("stage" => $g, "matches" => $matches);

    }

    /**
     * @Route("/admin/knockout/{id}")
     * @Template()
     */
    public function knockoutAction(Entity\Knockout $k)
    {
        $em = $this->getDoctrine()->getManager();
        $matches = $em->getRepository("InsaLanTournamentBundle:Match")->getByKnockout($k);

        return array("knockout" => $k, "matches" => $matches);

    }

    /**
     * @Route("/admin/match/{id}/setState/{state}")
     */
    public function match_setStateAction(Entity\Match $m, $state)
    {
        $em = $this->getDoctrine()->getManager();
        $state = intval($state);

        if($state !== Entity\Match::STATE_UPCOMING &&
           $state !== Entity\Match::STATE_ONGOING &&
           $state !== Entity\Match::STATE_FINISHED)
            throw new ControllerException("Unexpected argument state");

        $m->setState($state);
        $this->updateMatch($m);
        $em->persist($m);
        $em->flush();

        return $this->getReturnRedirect($m);

    }

    /**
     * @Route("/admin/match/{id}/addRound")
     */
    public function match_addRoundAction(Entity\Match $m)
    {
        $request = $this->get('request');
        if($request->getMethod() !== "POST")
            throw new ControllerException("Bad method");

        $score1 = intval($request->get('score1'));
        $score2 = intval($request->get('score2'));

        $r = new Entity\Round();
        $r->setScore1($score1);
        $r->setScore2($score2);
        $r->setMatch($m);
        $m->addRound($r);

        $em = $this->getDoctrine()->getManager();
        $em->persist($r);
        $em->persist($m);
        $em->flush();

        return $this->getReturnRedirect($m);
    }

    /**
     * @Route("/admin/match/{id}/reset")
     */
    public function match_resetAction(Entity\Match $m)
    {
        $em = $this->getDoctrine()->getManager();

        foreach($m->getRounds() as $r) {
            $m->removeRound($r);
            $em->remove($r);
        }

        $em->persist($m);
        $em->flush();
        return $this->getReturnRedirect($m);
    }

    /**
     * @Route("/{id}/admin/create/ko")
     */
    public function create_koAction(Entity\Tournament $tournament)
    {
        $form = $this->getFormKo($tournament->getId());
        $form->handleRequest($this->getRequest());

        if(!$form->isValid())
            throw new ControllerException("Not allowed");

        $data = $form->getData();

        if($data['size'] <= 1)
            throw new ControllerException("Not allowed");

        $em = $this->getDoctrine()->getManager();

        $ko = new Entity\Knockout();
        $ko->setName($data['name']);
        $ko->setTournament($tournament);
        $em->persist($ko);
        $em->flush();

        $em->getRepository("InsaLanTournamentBundle:KnockoutMatch")
           ->generateMatches($ko, $data['size'], $data['double']);

        return $this->redirect($this->generateUrl(
                'insalan_tournament_admin_knockout_view',
                array('id' => $ko->getId())));
    }

    /**
     * @Route("/admin/knockout/{id}/view")
     * @Template()
     */
    public function knockout_viewAction(Entity\Knockout $ko)
    {
        $em = $this->getDoctrine()->getManager();

        $depth    = $em->getRepository('InsaLanTournamentBundle:KnockoutMatch')->getLeftDepth($ko);
        $children = pow(2, $depth + ($ko->getDoubleElimination() ? 0 : 1));

        if($this->get('request')->getMethod() === "POST") {

            $root = $em->getRepository('InsaLanTournamentBundle:KnockoutMatch')->getRoot($ko);
            if($ko->getDoubleElimination()) {
                $root = $root->getChildren()->get(0);
            }
            $koMatches = $em->getRepository('InsaLanTournamentBundle:KnockoutMatch')->getLvlChildren($root, $depth);

            for($i = 0; $i < $children / 2; $i++) {

                $part1 = $this->get('request')->request->get("participant_".($i*2+1));
                $part2 = $this->get('request')->request->get("participant_".($i*2+2));

                $part1 = $em->getRepository('InsaLanTournamentBundle:Participant')->findOneById($part1);
                $part2 = $em->getRepository('InsaLanTournamentBundle:Participant')->findOneById($part2);

                // Is there any previous match ?
                $km = $koMatches[$i];

                if($km->getMatch()) {
                    $match = $km->getMatch();
                }
                else {
                    $match = new Entity\Match();
                    $match->setState(Entity\Match::STATE_UPCOMING);
                    $km->setMatch($match);
                    $match->setKoMatch($km);
                }

                $match->setPart1($part1);
                $match->setPart2($part2);

                $em->persist($km);
                $em->persist($match);

            }

            // Propagate for potential empty matches

            $em->getRepository('InsaLanTournamentBundle:KnockoutMatch')->propagateVictoryAll($ko);
            $em->flush();

        }

        $ko->jsonData = $em->getRepository('InsaLanTournamentBundle:KnockoutMatch')->getJson($ko);

        // Get participants in this tournament
        $participants = $em->getRepository('InsaLanTournamentBundle:Participant')->findByTournament($ko->getTournament());
        $a = array(null => '');
        foreach ($participants as $p) {
            $a[$p->getId()] = $p->getName();
        }

        asort($a);

        // No form here, because Symfony is stupid and does not allow dynamic number a field without verbose and boring class.

        return array('knockout' => $ko, 'participants' => $a, 'children' => $children);
    }


    private function updateMatch(Entity\Match $match) {

        if($match->getState() !== Entity\Match::STATE_FINISHED || !$match->getKoMatch())
            return;

        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository('InsaLanTournamentBundle:KnockoutMatch');
        $repository->propagateVictory($match->getKoMatch());

        $ko = $match->getKoMatch()->getKnockout();

        if($ko->getDoubleElimination()) {
            // deal with it.
            $repository->propagateFromNode(
                $repository
                ->getRoot($ko)
                ->getChildren()
                ->get(1)); // only update the hard part of the tree
        }

    }

    private function getFormKo($tournament) {
        return $this->createFormBuilder()
                    ->add('name', 'text', array("label" => "Nom"))
                    ->add('size', 'integer', array("label" => "Taille", "precision" => 0))
                    ->add('double', 'checkbox', array("label" => "Double Elimination", "required" => false))
                    ->setAction($this->generateUrl('insalan_tournament_admin_create_ko',
                                                  array('id' => $tournament)))
                    ->getForm();
    }

    private function getReturnRedirect(Entity\Match $m) {
        if($m->getKoMatch()) {
            return $this->redirect($this->generateUrl(
                'insalan_tournament_admin_knockout',
                array('id' => $m->getKoMatch()->getKnockout()->getId())));
        }

        return $this->redirect($this->generateUrl(
                'insalan_tournament_admin_stage',
                array('id' => $m->getGroup()->getStage()->getId())));
    }
}
