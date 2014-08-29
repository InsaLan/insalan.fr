<?php

namespace InsaLan\TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use InsaLan\TournamentBundle\Entity;

class AdminController extends Controller
{
    /**
     * @Route("/admin/group")
     * @Template()
     */
    public function group_indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tournaments = $em->getRepository('InsaLanTournamentBundle:Tournament')->findAll();
        $a = array(null => '');
        foreach ($tournaments as $t) {
            $a[$t->getId()] = $t->getName();
        }

        $form = $this->createFormBuilder()
            ->add('tournament', 'choice', array('label' => 'Tournoi', 'choices' => $a))
            ->getForm();

        $tournament = $stages = null;

        if ($this->getRequest()->isMethod('POST')) {
            $form->handleRequest($this->getRequest());
            if ($form->isValid()) {
                // Get Tournament object
                $data = $form->getData();
                foreach ($tournaments as &$t) {
                    if ($t->getId() == $data['tournament']) {
                        $tournament = $t;
                    }
                }

                // Find group stages and groups for this tournament
                $stages = $em->getRepository('InsaLanTournamentBundle:GroupStage')
                    ->getByTournament($tournament);

                foreach ($stages as $s) {
                    foreach ($s->getGroups() as $g) {
                        $g->countWins();
                    }
                }
            }
        }

        return array(
            'form'       => $form->createView(),
            'tournament' => $tournament,
            'stages'     => $stages
        );
    }


    /**
     * @Route("/admin/group/{id}", requirements={"id" = "\d+"})
     * @Template()
     */
    public function group_editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        try {
            $group = $em->getRepository('InsaLanTournamentBundle:Group')->getById($id);
        } catch (\Doctrine\ORM\NoResultException $e) {
            throw new NotFoundHttpException('InsaLan\\TournamentBundle\\Entity\\Group object not found.');
        }

        $form = $this->addParticipantForm($group);

        return array('group' => $group, 'form' => $form->createView());
    }

    protected function addParticipantForm(Entity\Group $group)
    {
        $form = $this->createFormBuilder()
            ->add('participant', 'text')
            ->setAction($this->generateUrl(
                'insalan_tournament_admin_group_addparticipant', array('id' => $group->getId())))
            ->getForm();

        return $form;
    }

    /**
     * @Route("/admin/group/{id}/add", requirements={"_method"="post", "id" = "\d+"})
     */
    public function group_addParticipantAction(Entity\Group $group)
    {
        $form = $this->addParticipantForm($group);

        if ($this->getRequest()->isMethod('POST')) {
            $form->handleRequest($this->getRequest());
            if ($form->isValid()) {
                $data = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $participant = $em->getRepository('InsaLanTournamentBundle:Participant')
                    ->findByName($data['participant']);

                if ($participant) {
                    $participant = $participant[0];
                    /*$matches = $em->getRepository('InsaLanTournamentBundle:GroupMatch')
                        ->findByGroup($group);

                    $found = false;
                    foreach ($matches as $m) {
                        $p1 = $m->getMatch();
                        $p2 = $p1->getPart2();
                        $p1 = $p1->getPart1();

                        if ($p1 == $participant || $p2 == $participant) {
                            $this->get('session')->getFlashBag()->add('error',
                                'Participant already in group.');
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        $parts = array();
                        foreach ($matches as $m) {
                            $p1 = $m->getMatch();
                            $p2 = $p1->getPart2();
                            $p1 = $p1->getPart1();

                            foreach (array($p1, $p2) as $p) {
                                if (!in_array($p->getId(), $parts)) {
                                    $parts[] = $p->getId();

                                    $m = new Entity\Match;
                                    $m->setPart1($p);
                                    $m->setPart2($participant);
                                    $em->persist($m);

                                    $gm = new Entity\GroupMatch;
                                    $gm->setGroup($group);
                                    $gm->setMatch($m);
                                    $em->persist($gm);

                                    $em->flush();
                                }
                            }
                        }

                        $this->get('session')->getFlashBag()->add('info', 'Participant added.');
                    }
                    */

                    if (!$group->hasParticipant($participant)) {
                        $group->addParticipant($participant);
                        $em->persist($group);
                        $em->flush();
                        $this->get('session')->getFlashBag()->add('info', 'Participant added.');
                    }
                    else {
                        $this->get('session')->getFlashBag()->add('error', 'Participant already in group.');
                    }
                }
                else {
                    $this->get('session')->getFlashBag()->add('error', 'Participant not found.');
                }
            }
        }

        return $this->redirect($this->generateUrl(
            'insalan_tournament_admin_group_edit',
            array('id' => $group->getId())));
    }

    /**
     * @Route("/admin/group/{id}/addlist", requirements={"id" = "\d+"})
     */
    public function group_addParticipantListAction($id)
    {

    }

    /**
     * @Route("/admin/group/{group}/delete/{participant}")
     */
    public function group_deleteParticipantAction(Entity\Group $group, Entity\Participant $participant)
    {
        $em = $this->getDoctrine()->getManager();
        if ($group->removeParticipant($participant)) {
            $em->persist($group);
            $em->flush();

            $this->get('session')->getFlashBag()->add('info',
                'Participant "'.$participant->getName().'" removed.');
        }
        else {
            $this->get('session')->getFlashBag()->add('error',
                'Participant not found.');
        }

        return $this->redirect($this->generateUrl(
            'insalan_tournament_admin_group_edit',
            array('id' => $group->getId())));
    }
}
