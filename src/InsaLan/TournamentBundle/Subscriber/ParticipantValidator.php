<?php

namespace InsaLan\TournamentBundle\Subscriber;

use Doctrine\Common\EventSubscriber; 
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use InsaLan\TournamentBundle\Entity\Team;
use InsaLan\TournamentBundle\Entity\Participant;
use InsaLan\TournamentBundle\Entity\Player;
use InsaLan\InsaLanBundle\Service\FreeSlots;

class ParticipantValidator implements EventSubscriber
{   
    private $updated_teams;

    public function __construct()
    {
        $this->updated_teams = [];
    }

    public function getSubscribedEvents()
    {
        return array(
            'preUpdate',
            'postFlush'
        );
    }

    /**
     * Needs a huge refactor, really ugly.
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Player) {
            $em = $args->getEntityManager();
            $teams = $entity->getTeam();
            foreach($teams as $team) { 
                $this->validateTeam($team,$em);
            }

            $validatedTeams = $em
                ->getRepository('InsaLanTournamentBundle:Team')
                ->findByValidated(Participant::STATUS_VALIDATED);
            foreach($validatedTeams as $team) {
                $this->unValidateTeam($team,$em);
            }
        }

    }

    public function preRemove(LifecycleEventArgs $args)
    {
        var_dump(get_class($args->getEntity()));
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if($this->updated_teams) { //execute the save if needed.
            $em = $args->getEntityManager();
            foreach($this->updated_teams as $team) {
                $em->persist($team);
            }
            $this->updated_teams = []; //put this ABSOLUTELY BEFORE the flush.
            $em->flush();
        }
    }

    protected function validateTeam($team,$em) {                
        if ($team->getCaptain() === null && $team->getPlayers()->count() > 0) { 
            $team->setCaptain($team->getPlayers()->first());
        }
        if($team->getPlayers()->count() >= $team->getTournament()->getTeamMinPlayer()) {
            //Ready for validation
            $freeSlots = $em
                ->getRepository('InsaLanTournamentBundle:Tournament')
                ->getFreeSlots($team->getTournament()->getId());
            if($freeSlots > 0)
                $team->setValidated(Participant::STATUS_VALIDATED);
            else
                $team->setValidated(Participant::STATUS_WAITING);
        }

        $this->updated_teams[] = $team; //register for further save
    }

    protected function unValidateTeam($team,$em) {
        if($team->getPlayers()->count() < $team->getTournament()->getTeamMinPlayer()) {
            if($team->getValidated() === Participant::STATUS_VALIDATED) {
                //If previously was validated, push another team in validated state.
                $waitingTeams = $em
                    ->getRepository('InsaLanTournamentBundle:Tournament')
                    ->selectWaitingParticipant($team->getTournament()->getId());
                foreach($waitingTeams as $waitingTeam) {
                    $waitingTeam->setValidated(Participant::STATUS_VALIDATED);
                    $this->updated_teams[] = $waitingTeam;
                }
            }

            $team->setValidated(Participant::STATUS_PENDING);
            $this->updated_teams[] = $team; //register for further save
        }
    }
}

?>
