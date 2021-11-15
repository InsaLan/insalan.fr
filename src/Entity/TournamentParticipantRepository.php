<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;


class TournamentParticipantRepository extends EntityRepository
{

    public function findByUser(\App\Entity\User $u) {
        $em = $this->getEntityManager();

        $query = $em->createQuery("
            SELECT pa
            FROM InsaLanTournamentBundle:Participant pa, InsaLanTournamentBundle:Player pl
            WHERE pl.user = :user AND pa.id = pl.id AND pa.tournament IS NOT NULL
        ")->setParameter('user', $u);
        $query->execute();

        $result1 = $query->getResult();

        $query = $em->createQuery("
            SELECT pa
            FROM InsaLanTournamentBundle:Participant pa,
                 InsaLanTournamentBundle:Player pl

            JOIN pl.team t

            WHERE pl.user = :user
              AND pa.id = t.id
              AND pa.tournament IS NOT NULL
        ")->setParameter('user', $u);

        $query->execute();
        
        $result2 = $query->getResult();
        
        $query = $em->createQuery("
            SELECT pa
            FROM InsaLanTournamentBundle:Participant pa,
                 InsaLanTournamentBundle:Manager ma

            JOIN ma.participant t

            WHERE ma.user = :user
              AND pa.id = t.id
              AND pa.tournament IS NOT NULL
        ")->setParameter('user', $u);

        $query->execute();
        
        $result3 = $query->getResult();

        $query = $em->createQuery("
            SELECT pa
            FROM InsaLanTournamentBundle:Participant pa,
                 InsaLanTournamentBundle:Bundle bu,
                 InsaLanTournamentBundle:Player pl

            JOIN pl.pendingRegistrable t

            WHERE pl.user = :user
              AND pa.id = pl.id
              AND bu.id = t.id
        ")->setParameter('user', $u);

        $query->execute();

        $result4 = $query->getResult();

        return new ArrayCollection(array_merge($result1, $result2, $result3, $result4));

    }

    public function findOneByUserAndTournament(\App\Entity\User $u, Tournament $t) {
        $participants = $this->findByUser($u);
        // TODO : do this is SQL only (when stable)
        foreach($participants as $p) {
            if($p->getTournament()->getId() === $t->getId())
                return $p;
        }

        return null;
    }

    public function findByRegistrable(Registrable $r) {
        $em = $this->getEntityManager();

        $query = $em->createQuery("
            SELECT pa
            FROM InsaLanTournamentBundle:Participant pa
            WHERE pa.tournament = :tournament
        ")->setParameter('tournament', $r);

        $query->execute();

        $result1 = $query->getResult();

        $query = $em->createQuery("
            SELECT pa
            FROM InsaLanTournamentBundle:Participant pa,
                 InsaLanTournamentBundle:Player pl,
                 InsaLanTournamentBundle:Bundle bu
            WHERE pa.id = pl.id
              AND pl.pendingRegistrable = :registrable
              AND bu.id = :registrable
        ")->setParameter('registrable', $r);

        $query->execute();

        $result2 = $query->getResult();

        return new ArrayCollection(array_merge($result1, $result2));
    }

}
