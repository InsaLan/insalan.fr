<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;




class ParticipantRepository extends EntityRepository
{

    public function findByUser(\App\Entity\User $u) {
        $em = $this->getEntityManager();

        $query = $em->createQuery("
            SELECT pa
            FROM \App\Entity\Participant pa, \App\Entity\Player pl
            WHERE pl.user = :user AND pa.id = pl.id AND pa.tournament IS NOT NULL
        ")->setParameter('user', $u);
        $query->execute();

        $result1 = $query->getResult();

        $query = $em->createQuery("
            SELECT pa
            FROM \App\Entity\Participant pa,
                 \App\Entity\Player pl

            JOIN pl.team t

            WHERE pl.user = :user
              AND pa.id = t.id
              AND pa.tournament IS NOT NULL
        ")->setParameter('user', $u);

        $query->execute();
        
        $result2 = $query->getResult();
        
        $query = $em->createQuery("
            SELECT pa
            FROM \App\Entity\Participant pa,
                 \App\Entity\TournamentManager ma

            JOIN ma.participant t

            WHERE ma.user = :user
              AND pa.id = t.id
              AND pa.tournament IS NOT NULL
        ")->setParameter('user', $u);

        $query->execute();
        
        $result3 = $query->getResult();

        $query = $em->createQuery("
            SELECT pa
            FROM \App\Entity\Participant pa,
                 \App\Entity\TournamentBundle bu,
                 \App\Entity\Player pl

            JOIN pl.pendingRegistrable t

            WHERE pl.user = :user
              AND pa.id = pl.id
              AND bu.id = t.id
        ")->setParameter('user', $u);

        $query->execute();

        $result4 = $query->getResult();

        return new ArrayCollection(array_merge($result1, $result2, $result3, $result4));

    }

    public function findOneByUserAndTournament(\App\Entity\User $u, \App\Entity\Tournament $t) {
        $participants = $this->findByUser($u);
        // TODO : do this is SQL only (when stable)
        foreach($participants as $p) {
            if($p->getTournament()->getId() === $t->getId())
                return $p;
        }

        return null;
    }

    public function findByRegistrable(\App\Entity\Registrable $r) {
        $em = $this->getEntityManager();

        $query = $em->createQuery("
            SELECT pa
            FROM \App\Entity\Participant pa
            WHERE pa.tournament = :tournament
        ")->setParameter('tournament', $r);

        $query->execute();

        $result1 = $query->getResult();

        $query = $em->createQuery("
            SELECT pa
            FROM \App\Entity\Participant pa,
                 \App\Entity\Player pl,
                 \App\Entity\TournamentBundle bu
            WHERE pa.id = pl.id
              AND pl.pendingRegistrable = :registrable
              AND bu.id = :registrable
        ")->setParameter('registrable', $r);

        $query->execute();

        $result2 = $query->getResult();

        return new ArrayCollection(array_merge($result1, $result2));
    }

}
