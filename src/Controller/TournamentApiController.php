<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Player;
use App\Entity\TournamentTeam;
use App\Entity\Tournament;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @Route("/tournament/admin")
 */
class TournamentApiController extends Controller
{

    /**
     * @Route("/email")
     * @Template()
     */
    public function emailAction(Request $request)
    {
        $tournaments = $this->getDoctrine()->getRepository('App\Entity\Tournament')->findThisYearTournaments(10);

        // Patch to switch from Symfony2 to Symfony3. We have to switch keys and values in arrays for choices.
        $playerStatusChoices = array_flip(Participant::getStatuses());
        $tournamentsChoices = array();

        foreach ($tournaments as $t) {
            $tournamentsChoices[$t->getName()] = array_keys($tournaments, $t, true)[0];// Close your eyes, it works.
        }


        $form = $this->createFormBuilder()
            ->add('shortName', ChoiceType::class, array(
                'choices' => $tournamentsChoices,
                'label' => 'Nom du tournois',
                'expanded' => false,
                'multiple' => false,
                'required' => true,
            ))
            ->add('playerStatus', ChoiceType::class, array(
                'choices' => $playerStatusChoices,
                'label' => 'Status des joueurs',
                'expanded' => false,
                'multiple' => false,
                'required' => true,
            ))
            ->add('Give me', SubmitType::class)
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
                        if ($user) $emailList[] = $user->getEmail();
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
                            if ($user) $emailList[] = $user->getEmail();
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
