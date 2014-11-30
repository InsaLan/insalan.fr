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
     * @Route("/{id}/admin/group", requirements={"id" = "\d+"})
     * @Template()
     */
    public function group_indexAction($id = null)
    {
        $em = $this->getDoctrine()->getManager();

        $tournaments = $em->getRepository('InsaLanTournamentBundle:Tournament')->findAll();
        $a = array(null => '');
        foreach ($tournaments as $t) {
            $a[$t->getId()] = $t->getName();
        }

        $form = $this->createFormBuilder()
            ->add('tournament', 'choice', array('label' => 'Tournoi', 'choices' => $a))
            ->setAction($this->generateUrl('insalan_tournament_admin_group_index'))
            ->getForm();

        $tournament = $stages = null;
        $data = null;

        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $data = $form->getData();
            return $this->redirect($this->generateUrl(
                'insalan_tournament_admin_group_index_1',
                array('id' => $data['tournament'])));
        }
        else if (null !== $id) {
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

            foreach ($stages as $s) {
                foreach ($s->getGroups() as $g) {
                    $g->countWins();
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
        /*$form = $this->addParticipantForm($group);

        if ($this->getRequest()->isMethod('POST')) {
            $form->handleRequest($this->getRequest());
            if ($form->isValid()) {
                $data = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $participant = $em->getRepository('InsaLanTournamentBundle:Participant')
                    ->findByName($data['participant']);

                if ($participant) {
                    $participant = $participant[0];

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
        }*/

        $this->get('session')->getFlashBag()->add('error',
                'Not ready for that.');

        return $this->redirect($this->generateUrl(
            'insalan_tournament_admin_group_edit',
            array('id' => $group->getId())));
    }

    /**
     * @Route("/admin/group/{group}/delete/{participant}")
     */
    public function group_deleteParticipantAction(Entity\Group $group, Entity\Participant $participant)
    {   

        
        $this->get('session')->getFlashBag()->add('error',
                'Not ready for that.');

        /*$em->persist($group);

        $em = $this->getDoctrine()->getManager();
        if ($group->removeParticipant($participant)) {
            $em->getRepository('InsaLanTournamentBundle:GroupMatch')->removeParticipant($group, $participant);
            $em->persist($group);
            $em->flush();

            $this->get('session')->getFlashBag()->add('info',
                'Participant "'.$participant->getName().'" removed.');
        }
        else {
            $this->get('session')->getFlashBag()->add('error',
                'Participant not found.');
        }*/

        return $this->redirect($this->generateUrl(
            'insalan_tournament_admin_group_edit',
            array('id' => $group->getId())));
    }
}
