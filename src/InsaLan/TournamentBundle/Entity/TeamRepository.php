<?php

namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

use InsaLan\TournamentBundle\Entity\Participant;

class TeamRepository extends EntityRepository
{
    public function getAllTeams() {
        $em = $this->getEntityManager();

        $query = $em->createQuery("
            SELECT partial t.{id,name,validated}, partial p.{id,lolName,lolId} 
            FROM InsaLanTournamentBundle:Team t
            JOIN t.players p
            ");
        $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
        $query->execute();
        return $query->getResult();
    }

    public function getWaitingTeams() {
        $em = $this->getEntityManager();

        $query = $em->createQuery("
            SELECT partial t.{id,name,validated}, partial p.{id,lolName,lolId}, partial to.{id,teamMinPlayer} 
            FROM InsaLanTournamentBundle:Team t
            JOIN t.players p
            JOIN t.tournament to
            WHERE t.validated = :state
            ")->setParameter('state', Participant::STATUS_VALIDATED);

        //$query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
        $query->execute();
        return $query->getResult();
    }
}
