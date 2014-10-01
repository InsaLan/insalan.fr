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
    const TEAM_SIZE = 5;

    private $updated_teams;
    private $fs;

    public function __construct(FreeSlots $fs)
    {
        $this->updated_teams = [];
        $this->fs   = $fs;
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

            if(!$args->hasChangedField("team")) return;

            $em = $args->getEntityManager();

            $team = $entity->getTeam();
            if(!$team) $team = $args->getOldValue("team");

            if ($team->getCaptain() === null && $team->getPlayers()->count() > 0) { 
                $team->setCaptain($team->getPlayers()->first());
            }

            if($team->getPlayers()->count() === self::TEAM_SIZE) {
                //Ready for validation
                if($this->fs->get() > 0)
                    $team->setValidated(Participant::STATUS_VALIDATED);
                else
                    $team->setValidated(Participant::STATUS_WAITING);
            } else {
                //Not Ready for validation
                if($team->getValidated() === Participant::STATUS_VALIDATED) {
                    //If previously was validated, push another team in validated state.
                    $validated = $this->fs->selectWaitingTeam();
                    if($validated !== null) {
                        $validated->setValidated(Participant::STATUS_VALIDATED);
                        $this->updated_teams[] = $validated;
                    }
                }
                
                $team->setValidated(Participant::STATUS_PENDING);

            }

            $this->updated_teams[] = $team; //register for further save
        }

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
}

?>