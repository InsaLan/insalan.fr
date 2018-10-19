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

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ApiController extends Controller
{

    /**
     * @Route("/admin/email")
     * @Template()
     */
    public function listEmailAction(Request $request)
    {
        $tournaments = $this->getDoctrine()->getRepository('InsaLanTournamentBundle:Tournament')->findThisYearTournaments();

        // Patch to switch from Symfony2 to Symfony3. We have to switch keys and values in arrays for choices.
        $playerStatusChoices = array_flip(Participant::getStatuses());
        $tournamentsChoices = array();

        foreach ($tournaments as $t) {
            $tournamentsChoices[$t->getName()] = array_keys($tournaments, $t, true)[0];// Close your eyes, it works.
        }


        $form = $this->createFormBuilder()
            ->add('shortName', ChoiceType::class, array(
                'choices' => $tournamentsChoices,
                'choices_as_values' => true,
                'label' => 'Nom du tournois',
                'expanded' => false,
                'multiple' => false,
                'required' => true,
            ))
            ->add('playerStatus', ChoiceType::class, array(
                'choices' => $playerStatusChoices,
                'choices_as_values' => true,
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
                        if ($team->getValidated() !== $statusRequired) {
                            continue;
                        }

                        /** @var Player[] $players */
                        $players = $team->getPlayers()->toArray();
                        foreach ($players as $player) {
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
