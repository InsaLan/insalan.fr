<?php

namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class TournamentRepository extends EntityRepository
{
    public function findOpened() {
        $query = $this->createQueryBuilder('t')
            ->where('t.registrationOpen < :now AND t.registrationClose > :now')
            ->setParameter('now', new \DateTime())
            ->getQuery();
        return $query->getResult();
    }
}
