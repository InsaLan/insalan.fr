<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\User;
use App\Entity\Tournament;

class TournamentManagerRepository extends EntityRepository
{
    public function findOneByUserAndPendingTournament(User $u, Tournament $t) {

        $q = $this->createQueryBuilder('m')
            ->where('m.user = :user AND m.tournament = :tournament')
            ->setParameter('user', $u)
            ->setParameter('tournament', $t);

        try {
            return $q->getQuery()->getSingleResult();
        } catch(\Exception $e) {
            return null;
        }
    }
}
