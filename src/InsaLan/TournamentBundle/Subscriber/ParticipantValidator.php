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

    private $updated_participants;

    public function __construct()
    {
        $this->updated_participants = [];
    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
            'postFlush',
            'preRemove'
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->preUpdate($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {

        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof Player) {
            if ($entity->getTournament() !== null && $entity->getValidated() !== Participant::STATUS_VALIDATED) {
                $this->validatePlayer($entity, $em);
            }

            foreach ($entity->getTeam() as $t) {
                $this->teamLogic($t, $em);
            }
        }
    }

    //Team Validation depends only of Players, so we can reasonnably execute it only when a user is updated. I guess.
    private function teamLogic($team, $em)
    {
        if ($team->getCaptain() === null && $team->getPlayers()->count() > 0) {
            $team->setCaptain($team->getPlayers()->first());
            $this->updated_participants[] = $team;
        }

        if ($team->getValidated() !== Participant::STATUS_VALIDATED) {
            $this->validateTeam($team, $em);
        } else {
            $this->unValidateTeam($team, $em);
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Participant) {
            if ($entity->getTournament() !== null && $entity->getValidated() === Participant::STATUS_VALIDATED) {
                $em = $args->getEntityManager();

                if ($entity instanceof Player) {
                    $waitingPlayer = $em
                        ->getRepository('InsaLanTournamentBundle:Player')
                        ->getWaitingPlayer($entity->getTournament());

                    if ($waitingPlayer) {
                        $waitingPlayer->setValidated(Participant::STATUS_VALIDATED);
                        $this->updated_participants[] = $waitingPlayer;
                    }
                } else {
                    $waitingTeam = $em
                        ->getRepository('InsaLanTournamentBundle:Team')
                        ->getWaitingTeam($entity->getTournament());

                    if ($waitingTeam) {
                        $waitingTeam->setValidated(Participant::STATUS_VALIDATED);
                        $this->updated_participants[] = $waitingTeam;
                    }
                }
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if ($this->updated_participants) { //execute the save if needed.
            $em = $args->getEntityManager();
            foreach ($this->updated_participants as $team) {
                $em->persist($team);
            }
            $this->updated_participants = []; //put this ABSOLUTELY BEFORE the flush.
            $em->flush();
        }
    }

    protected function validatePlayer($player, $em)
    {

        if (!$player->getPaymentDone() && $player->getTournament()->getWebPrice() > 0) {
            return;
        }

        $freeSlots = $em
                ->getRepository('InsaLanTournamentBundle:Tournament')
                ->getFreeSlots($player->getTournament()->getId());

        if ($freeSlots > 0) {
            $player->setValidated(Participant::STATUS_VALIDATED);
        } else {
            $player->setValidated(Participant::STATUS_WAITING);
        }

        $this->updated_participants[] = $player; //register for further save
    }

    protected function validateTeam($team, $em)
    {
        

        if ($team->getPlayers()->count() >= $team->getTournament()->getTeamMinPlayer()) {
            // Check players payments
            $minPaymentNumbers = floor($team->getTournament()->getTeamMinPlayer() / 2) + 1;
            $sumPayments = 0;
            foreach ($team->getPlayers() as $p) {
                if ($p->getPaymentDone()) {
                    $sumPayments++;
                }
            }

            if ($sumPayments < $minPaymentNumbers && $team->getTournament()->getWebPrice() > 0) {
                return;
            }

            //Ready for validation
            $freeSlots = $em
                ->getRepository('InsaLanTournamentBundle:Tournament')
                ->getFreeSlots($team->getTournament()->getId());
            if ($freeSlots > 0) {
                $team->setValidated(Participant::STATUS_VALIDATED);
            } else {
                $team->setValidated(Participant::STATUS_WAITING);
            }

            $this->updated_participants[] = $team;
        }
    }

    protected function unValidateTeam($team, $em)
    {

        if ($team->getPlayers()->count() < $team->getTournament()->getTeamMinPlayer()) {
            $waitingTeam = $em
                    ->getRepository('InsaLanTournamentBundle:Team')
                    ->getWaitingTeam($team->getTournament());

            if ($waitingTeam) {
                $waitingTeam->setValidated(Participant::STATUS_VALIDATED);
                $this->updated_participants[] = $waitingTeam;
            }

            $team->setValidated(Participant::STATUS_PENDING);
            $this->updated_participants[] = $team;
        }
    }
}
