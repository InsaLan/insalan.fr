<?php

namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ManagerRepository extends EntityRepository
{
    public function findOneByUserAndPendingTournament(\InsaLan\UserBundle\Entity\User $u, Tournament $t)
    {

        $q = $this->createQueryBuilder('m')
            ->where('m.user = :user AND m.tournament = :tournament')
            ->setParameter('user', $u)
            ->setParameter('tournament', $t);

        try {
            return $q->getQuery()->getSingleResult();
        } catch (\Exception $e) {
            return null;
        }
    }
}
