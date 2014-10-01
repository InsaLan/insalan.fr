<?php

namespace InsaLan\TournamentBundle\Subscriber;

use Doctrine\Common\EventSubscriber; 
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use InsaLan\TournamentBundle\Entity\Team;
use InsaLan\TournamentBundle\Entity\Participant;
use InsaLan\TournamentBundle\Entity\Player;

class ParticipantValidator implements EventSubscriber
{	
	const TEAM_SIZE = 5;

	private $team = null;

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
		    $team->setValidated($team->getPlayers()->count() === self::TEAM_SIZE);

		    $this->team = $team; //register for further save
        }

    }

    public function postFlush(PostFlushEventArgs $args)
    {
    	if($this->team) { //execute the save if needed.
    		$em = $args->getEntityManager();
    		$em->persist($this->team);
            $this->team = null; //put this ABSOLUTELY BEFORE the flush.
    		$em->flush();
    	}
    }
}

?>