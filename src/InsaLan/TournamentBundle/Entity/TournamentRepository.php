<?php

namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

use InsaLan\TournamentBundle\Entity\Participant;

class TournamentRepository extends EntityRepository
{
    public function findOpened() {
        $query = $this->createQueryBuilder('t')
            ->where('t.registrationOpen <= :now AND t.registrationClose >= :now')
            ->setParameter('now', (new \DateTime())->setTime(00,00))
            ->getQuery();
        return $query->getResult();
    }

    public function getFreeSlots($tournamentId) {
        $tournament = $this->findOneById($tournamentId);
        $freeSlots = $tournament->getRegistrationLimit();
        foreach($tournament->getParticipants() as $participant) {
            if ($participant->getValidated() === Participant::STATUS_VALIDATED) {
                $freeSlots--;
            }
        }
        return $freeSlots;
    }

    public function selectWaitingParticipant($tournamentId) {
        $tournament = $this->findOneById($tournamentId);
        $participants = array();
        foreach($tournament->getParticipants() as $participant) {
            if ($participant->getValidated() === Participant::STATUS_WAITING) {
                $participants[] = $participant;
            }
        }
        return $participants;
    }
}
