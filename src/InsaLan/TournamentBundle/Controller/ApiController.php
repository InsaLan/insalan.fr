<?php

namespace InsaLan\TournamentBundle\Controller;

use InsaLan\TournamentBundle\Entity\Participant;
use InsaLan\TournamentBundle\Entity\Player;
use InsaLan\TournamentBundle\Entity\Team;
use InsaLan\TournamentBundle\Entity\Tournament;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends Controller
{

    /**
     * @Route("/admin/email")
     * @Template()
     */
    public function listEmailAction(Request $request)
    {
        $tournaments = $this->getDoctrine()->getRepository('InsaLanTournamentBundle:Tournament')->findThisYearTournaments();

        $form = $this->createFormBuilder()
            ->add('shortName', 'choice', array(
                'choices' => $tournaments,
                'label' => 'Nom du tournois',
                'expanded' => false,
                'multiple' => false,
                'required' => true,
            ))
            ->add('playerStatus', 'choice', array(
                'choices' => Participant::getStatuses(),
                'label' => 'Status des joueurs',
                'expanded' => false,
                'multiple' => false,
                'required' => true,
            ))
            ->add('Give me', 'submit')
            ->getForm()
        ;

        $form->handleRequest($request);
        $emailList = array();

        if ($form->isSubmitted() and $form->isValid()) {
            $formData = $form->getData();
            $indexTournament = $formData['shortName'];
            $statusRequired = $formData['playerStatus'];

            /** @var Tournament $tournament */
            $tournament = $tournaments[$indexTournament];
            $type = $tournament->getParticipantType();
            switch ($type) {
                case 'player':
                    $players = $tournament->getParticipants()->toArray();
                    /** @var Player[] $players */
                    foreach ($players as $player) {
                        if ($player->getValidated() !== $statusRequired) {
                            continue;
                        }

                        $user = $player->getUser();
                        $emailList[] = $user->getEmail();
                    }
                    break;
                case 'team':
                    /** @var Team[] $teams */
                    $teams = $tournament->getParticipants()->toArray();
                    foreach ($teams as $team) {
                        /** @var Player[] $players */
                        $players = $team->getPlayers()->toArray();
                        foreach ($players as $player) {
                            if ($player->getValidated() !== $statusRequired) {
                                continue;
                            }

                            $user = $player->getUser();
                            $emailList[] = $user->getEmail();
                        }
                    }
                    break;
                default:
                    return new \InvalidArgumentException('Ham, expected Tournament type : "player", "team"');
            }
        }

        return array(
            'form' => $form->createView(),
            'emailList' => $emailList,
        );
    }
}