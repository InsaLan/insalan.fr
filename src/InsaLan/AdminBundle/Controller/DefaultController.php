<?php

namespace InsaLan\AdminBundle\Controller;

use InsaLan\TournamentBundle\Entity\Group;
use Symfony\Component\HttpFoundation\Response;
use InsaLan\TournamentBundle\Entity\GroupStage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use InsaLan\PizzaBundle\Entity;
use InsaLan\ApiBundle\Http\JsonResponse;

class DefaultController extends Controller {
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction() {
        return array();
    }

    /**
     * @Route("/tournament")
     * @Template()
     */
    public function tournamentAction() {
        return array();
    }

    /**
     * @Route("/tournament/groupstage", name="groupstageRoute")
     * @Template()
     * Get all group stages (phases de poule)
     */
    public function tournamentGroupStageAction() {
        $em = $this->getDoctrine()->getManager(); // entity manager

        $groupStage = new GroupStage();
        $form = $this->createFormBuilder($groupStage)
            ->add('name')
            ->add('tournament', 'entity', array('class' => 'InsaLanTournamentBundle:Tournament'))
            ->add('save', 'submit', array('label' => 'Créer'))
            ->getForm();

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($groupStage); // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $em->flush(); // actually executes the queries (i.e. the INSERT query)
        }

        $groupStages = $em->getRepository('InsaLanTournamentBundle:GroupStage')->findAll();

        return array(
            'groupStages' => $groupStages,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/tournament/groupstage/remove/{id}")
     */
    public function tournamentGroupStageRemoveAction($id) {
        $em = $this->getDoctrine()->getManager(); // entity manager

        $groupStage = $em->getRepository('InsaLanTournamentBundle:GroupStage')->find($id);

        if($groupStage != null) {
            try {
                $em->remove($groupStage);
                $em->flush();
            } catch(\Exception $e) {
                // TODO message d'erreur si on n'arrive pas à enlever le groupstage ?
                return $this->redirectToRoute('groupstageRoute');
            }
        }

        return $this->redirectToRoute('groupstageRoute');
    }

    /**
     * @Route("/tournament/groupstage/modify/{id}")
     * @Template()
     */
    public function tournamentGroupStageModifyAction($id) {
        $em = $this->getDoctrine()->getManager(); // entity manager
        $groupStage = $em->getRepository('InsaLanTournamentBundle:GroupStage')->find($id);

        $form = $this->createFormBuilder($groupStage)
            ->add('name')
            ->add('tournament', 'entity', array('class' => 'InsaLanTournamentBundle:Tournament'))
            ->add('save', 'submit', array('label' => 'Modifier'))
            ->getForm();

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($groupStage); // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $em->flush(); // actually executes the queries (i.e. the INSERT query)
            return $this->redirectToRoute('groupstageRoute');
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/tournament/group")
     * @Template()
     * Get all groups (poules)
     */
    public function tournamentGroupAction() {
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('InsaLanTournamentBundle:Group')->findAll();

        return $this->render('InsaLanAdminBundle:Default:tournamentGroup.html.twig', array(
            'groups' => $group
        ));
    }

    /**
     * @Route("/tournament/match")
     * @Template()
     * Get all matches
     */
    public function tournamentMatchAction() {
        $em = $this->getDoctrine()->getManager();
        $match = $em->getRepository('InsaLanTournamentBundle:Match')->findAll();

        return $this->render('InsaLanAdminBundle:Default:tournamentMatch.html.twig', array(
            'match' => $match
        ));
    }

    /**
     * @Route("/pizza")
     * @Route("/pizza/{id}")
     * @Template()
     */
    public function pizzaAction() {
        // public function pizzaAction($id = null) {
        return array();
        // return $this->redirect($this->generateUrl("insalan_pizza_admin_index", array("id" => $id)));
    }

    /**
     * @Route("/web")
     * @Template()
     */
    public function webAction() {
        return array();
    }
}
