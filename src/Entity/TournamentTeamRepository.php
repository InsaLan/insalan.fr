<?php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

use App\Entity\Participant;

class TournamentTeamRepository extends EntityRepository
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
            SELECT partial t.{id,name,validated}, partial p.{id,gameName,gameId, paymentDone}
            FROM InsaLanTournamentBundle:Team t
            JOIN t.players p
            WHERE t.tournament = :tournament
            ORDER BY t.validationDate
            ");
        $query->setParameter('tournament', $t);
        $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
        $query->execute();
        return $query->getResult();
    }
 
    /**
     * Find a team of $team_name in the specified $tournament
     * @param  string     $team_name Name of the team to find
     * @param  Tournament $t         Tournament to look into
     * @return Team                  The matched team, if any
     */
    public function findOneByNameAndTournament($team_name, Tournament $t) {
        $em = $this->getEntityManager();

        $query = $em->createQuery("
            SELECT t 
            FROM InsaLanTournamentBundle:Team t
            WHERE t.name = :team_name AND t.tournament = :tournament
            ")
        ->setParameter('team_name', $team_name)
        ->setParameter('tournament', $t)
        ->setMaxResults(1);
        $query->execute();

        try {
            return $query->getSingleResult();
        }
        catch(\Exception $e) {
            return null;
        }
    }

    public function getWaitingTeam(Tournament $t) {

        $q = $this->createQueryBuilder('t')
             ->where('t.tournament = :tournament AND t.validated = :state')
             ->orderBy('t.validationDate')
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
