<?php

namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class TeamRepository extends EntityRepository
{
    public function getAllTeams() {
        $em = $this->getEntityManager();

        $query = $em->createQuery("
            SELECT partial t.{id,name,validated}, partial p.{id,name,lolId} 
            FROM InsaLanTournamentBundle:Team t
            JOIN t.players p
            ");
        $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
        $query->execute();
        return $query->getResult();
    }
}
