<?php

namespace App\Controller;

use App\Entity\TournamentGroup;
use App\Entity\TournamentGroupStage;
use App\Entity\TournamentMatch;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Repository\ParticipantRepository;
use App\Entity\Participant;


class AdminTournamentController extends Controller {
    /**
     * @Route("/tournament/groupstage", name="GroupStageAction")
     * Get all group stages (phases de poule)
     */
    public function tournamentGroupStageAction() {
        $em = $this->getDoctrine()->getManager(); // entity manager

        $groupStage = new TournamentGroupStage();
        $form = $this->createFormBuilder($groupStage)
            ->add('name')
            ->add('tournament', EntityType::class, array('class' => 'App\Entity\Tournament'))
            ->add('save', SubmitType::class, array('label' => 'Créer'))
            ->getForm();

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($groupStage); // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $em->flush(); // actually executes the queries (i.e. the INSERT query)
        }

        $groupStages = $em->getRepository('App\Entity\TournamentGroupStage')->findBy([], ['id' => 'DESC']);

        return $this->render('Tournament/tournamentGroupStage.html.twig', ['groupStages' => $groupStages, 'form' => $form->createView()]);
    }

    /**
     * @Route("/tournament/groupstage/remove/{id}", name="GroupStageRemoveAction")
     */
    public function tournamentGroupStageRemoveAction($id) {
        $em = $this->getDoctrine()->getManager(); // entity manager

        $groupStage = $em->getRepository('App\Entity\TournamentGroupStage')->find($id);

        if($groupStage != null) {
            try {
                $em->remove($groupStage);
                $em->flush();
            } catch(\Exception $e) {
                // TODO message d'erreur si on n'arrive pas à enlever le groupstage ?
                return $this->redirectToRoute('GroupStageAction');
            }
        }

        return $this->redirectToRoute('GroupStageAction');
    }

    /**
     * @Route("/tournament/groupstage/modify/{id}", name="GroupStageModifyAction")
     * @Template("Tournament/tournamentGroupStageModify.html.twig")
     */
    public function tournamentGroupStageModifyAction($id) {
        $em = $this->getDoctrine()->getManager(); // entity manager
        $groupStage = $em->getRepository('App\Entity\TournamentGroupStage')->find($id);

        $form = $this->createFormBuilder($groupStage)
            ->add('name')
            ->add('tournament', EntityType::class, array('class' => 'App\Entity\Tournament'))
            ->add('save', SubmitType::class, array('label' => 'Modifier'))
            ->getForm();

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($groupStage); // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $em->flush(); // actually executes the queries (i.e. the INSERT query)
            return $this->redirectToRoute('GroupStageAction');
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/tournament/group", name="GroupAction")
     * @Template("Tournament/tournamentGroup.html.twig")
     * Get all groups (poules)
     */
    public function tournamentGroupAction() {
        $em = $this->getDoctrine()->getManager();

        $group = new TournamentGroup();
        $form = $this->createFormBuilder($group)
            ->add('name')
            ->add('stage', EntityType::class, array('class' => 'App\Entity\TournamentGroupStage'))
            ->add('participants', EntityType::class, array(
              'query_builder' => function(ParticipantRepository $er) {
                                    return $er->createQueryBuilder('e')
                                              ->leftJoin('e.manager', 'ma')
                                              ->addSelect('ma')
                                              ->where('e.validated = :status')
                                              ->setParameter('status', Participant::STATUS_VALIDATED);
                                },
                'class' => 'App\Entity\Participant',
                'multiple' => true))
            ->add('statsType', ChoiceType::class, array(
                'label' => "Type de score",
                'choices' => array(
                    'Victoires/Défaites' => TournamentGroup::STATS_WINLOST,
                    'Somme des scores' => TournamentGroup::STATS_SCORE
                ),
                'required' => true))
            ->add('save', SubmitType::class, array('label' => 'Créer'))
            ->getForm();

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($group); // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $em->getRepository('App\Entity\TournamentGroup')->autoManageMatches($group);
            $em->flush(); // actually executes the queries (i.e. the INSERT query)
        }

        $groups = $em->getRepository('App\Entity\TournamentGroup')->findBy([], ['id' => 'DESC']);

        return array(
            'groups' => $groups,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/tournament/group/remove/{id}", name="GroupRemoveAction")
     */
    public function tournamentStageRemoveAction($id) {
        $em = $this->getDoctrine()->getManager(); // entity manager

        $group = $em->getRepository('App\Entity\TournamentGroup')->find($id);

        if($group != null) {
            try {
                $em->remove($group);
                $em->flush();
            } catch(\Exception $e) {
                return $this->redirectToRoute('GroupAction');
            }
        }

        return $this->redirectToRoute('GroupAction');
    }

    /**
     * @Route("/tournament/group/modify/{id}", name="GroupModifyAction")
     * @Template("Tournament/tournamentGroupModify.html.twig")
     */
    public function tournamentGroupModifyAction($id) {
        $em = $this->getDoctrine()->getManager(); // entity manager
        $group = $em->getRepository('App\Entity\TournamentGroup')->find($id);

        $form = $this->createFormBuilder($group)
            ->add('name')
            ->add('stage', EntityType::class, array('class' => 'App\Entity\TournamentGroupStage'))
            ->add('participants', EntityType::class, array(
              'query_builder' => function(ParticipantRepository $er) {
                                    return $er->createQueryBuilder('e')
                                              ->leftJoin('e.manager', 'ma')
                                              ->addSelect('ma')
                                              ->where('e.validated = :status')
                                              ->setParameter('status', Participant::STATUS_VALIDATED);
                                },
                'class' => 'App\Entity\Participant',
                'multiple' => true))
            ->add('statsType', ChoiceType::class, array(
                'label' => "Type de score",
                'choices' => array(
                    'Victoires/Défaites' => TournamentGroup::STATS_WINLOST,
                    'Somme des scores' => TournamentGroup::STATS_SCORE
                ),
                'required' => true))
            ->add('save', SubmitType::class, array('label' => 'Modifier'))
            ->getForm();

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($group); // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $em->getRepository('App\Entity\TournamentGroup')->autoManageMatches($group);
            $em->flush(); // actually executes the queries (i.e. the INSERT query)
            return $this->redirectToRoute('GroupAction');
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/tournament/match", name="MatchAction")
     * Get all matches
     */
    public function tournamentMatchAction() {
        return $this->redirect($this->generateUrl('app_tournamentadmin_index'));
    }
}
