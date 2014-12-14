<?php

namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;


class PlayerRepository extends EntityRepository
{

    public function findOneByUserAndPendingTournament(\InsaLan\UserBundle\Entity\User $u, Tournament $t) {
        
        $q = $this->createQueryBuilder('p')
            ->where('p.user = :user AND p.pendingTournament = :tournament')
            ->setParameter('user', $u)
            ->setParameter('tournament', $t);

        try {
            return $q->getQuery()->getSingleResult();
        }
        catch(\Exception $e) {
            return null;
        }

    }

    public function getWaitingPlayer(Tournament $t) {

        $q = $this->createQueryBuilder('p')
             ->where('p.tournament = :tournament AND p.validated = :state')
             ->orderBy('p.id')
             ->setParameter('tournament', $t)
             ->setParameter('state', Participant::STATUS_WAITING)
             ->setMaxResults(1);

        try {
            return $q->getQuery()->getSingleResult();
        }
        catch(\Exception $e) {
            return null;
        }

    }

}