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

        $tournament = $stages = $ko = null;
        $data = null;

        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $data = $form->getData();
            return $this->redirect($this->generateUrl(
                'insalan_tournament_admin_index_1',
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

            // Find knockout for this tournament
            $ko = $em->getRepository('InsaLanTournamentBundle:Knockout')
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
            'stages'     => $stages,
            'knockouts'  => $ko
        );
    }

}
