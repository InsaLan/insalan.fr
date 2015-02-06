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
            SELECT partial t.{id,name,validated}, partial p.{id,gameName,gameId} 
            FROM InsaLanTournamentBundle:Team t
            JOIN t.players p
            ");
        $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
        $query->execute();
        return $query->getResult();
    }
 
    public function getTeamsForTournament(Tournament $t) {
        $em = $this->getEntityManager();

        $query = $em->createQuery("
            SELECT partial t.{id,name,validated}, partial p.{id,gameName,gameId} 
            FROM InsaLanTournamentBundle:Team t
            JOIN t.players p
            WHERE t.tournament = :tournament
            ");
        $query->setParameter('tournament', $t);
        $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
        $query->execute();
        return $query->getResult();
    }
 
    public function getWaitingTeam(Tournament $t) {

        $q = $this->createQueryBuilder('t')
             ->where('t.tournament = :tournament AND t.validated = :state')
             ->orderBy('t.id')
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


    /*public function getWaitingTeams() {
        $em = $this->getEntityManager();

        $query = $em->createQuery("
            SELECT partial t.{id,name,validated}, partial p.{id,gameName,gameId}, partial to.{id,teamMinPlayer} 
            FROM InsaLanTournamentBundle:Team t
            JOIN t.players p
            JOIN t.tournament to
            WHERE t.validated = :state
            ")->setParameter('state', Participant::STATUS_VALIDATED);

        //$query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
        $query->execute();
        return $query->getResult();
    }*/
}
