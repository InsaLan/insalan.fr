<?php

namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;


class ParticipantRepository extends EntityRepository
{

    public function findByUser(\InsaLan\UserBundle\Entity\User $u) {
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

        return new ArrayCollection(array_merge($result1, $result2));

    }

    public function findOneByUserAndTournament(\InsaLan\UserBundle\Entity\User $u, Tournament $t) {
        $participants = $this->findByUser($u);
        // TODO : do this is SQL only (when stable)
        foreach($participants as $p) {
            if($p->getTournament()->getId() === $t->getId())
                return $p;
        }
    }

}