<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;

use App\Entity\Participant;
use App\Entity\Tournament;
use App\Entity\Registrable;
use App\Entity\User;

class PlayerRepository extends EntityRepository
{

    public function findOneByUserAndPendingRegistrable(User $u, Registrable $r) {

        $q = $this->createQueryBuilder('p')
            ->where('p.user = :user AND p.pendingRegistrable = :registrable')
            ->setParameter('user', $u)
            ->setParameter('registrable', $r);

        try {
            return $q->getQuery()->getSingleResult();
        }
        catch(\Exception $e) {
            return null;
        }

    }

    public function getPlayersForTournament(Tournament $tournament) {
        $em = $this->getEntityManager();

        $bundles = $this->_em->createQueryBuilder()
                        ->select('bu.id')
                        ->from('\App\Entity\TournamentBundle', 'bu')
                        ->innerJoin('bu.tournaments', 't')
                        ->where('t.id = :tournament');

        $q = $this->createQueryBuilder('p');
        $q = $q->where('p.tournament = :tournament')
               ->orWhere(
                   $q->expr()->in(
                       'p.pendingRegistrable',
                        $bundles->getDql()
                   )
               )
               ->orderBy('p.validationDate')
               ->setParameter('tournament', $tournament);

        return $q->getQuery()->execute();
    }

    public function getAllPlayersForTournament(Tournament $tournament) {

        $em = $this->getEntityManager();

       // Execute player research

        $q = $this->createQueryBuilder('p')
                    ->innerJoin('p.user', 'u1')
                    ->addSelect('u1')
                    ->leftJoin('p.team', 't')
                    ->addSelect('t')
                    ->leftJoin('p.manager', 'm1')
                    ->addSelect('m1')
                    ->leftJoin('t.manager', 'm2')
                    ->addSelect('m2')
                    ->leftJoin('m1.user', 'u2')
                    ->addSelect('u2')
                    ->leftJoin('m2.user', 'u3')
                    ->addSelect('u3')
                    ->where('p.tournament = :tournament')
                    ->orWhere('p.pendingRegistrable = :tournament')
                    ->addOrderBy('t.name')
                    ->addOrderBy('p.gameName')
                    ->setParameter('tournament', $tournament);

        $players = $q->getQuery()->execute();
        $out = array();
        foreach($players as $player) {
            if($player->getValidated() === Participant::STATUS_VALIDATED) {
                $out[] = $player;
                continue;
            }

            foreach($player->getTeam() as $team) {
                if($team->getValidated() === Participant::STATUS_VALIDATED) {
                    $out[] = $player;
                    break;
                }
            }
        }

        return $out;
    }

    public function getWaitingPlayer(Tournament $t) {

        $q = $this->createQueryBuilder('p')
             ->where('p.tournament = :tournament AND p.validated = :state')
             ->orderBy('p.validationDate')
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

    public function getValidatedTeamPlayers(Array $tournaments) {

        $q = $this->createQueryBuilder('p')
             ->where('p.paymentDone = 1')
             ->andWhere('p.pendingRegistrable IN (:tournaments)')
             ->andWhere('p.eTicket is NULL')
             ->orderBy('p.validationDate')
             ->setParameter('tournaments', $tournaments);

        $players = $q->getQuery()->execute();
        $out = array();
        foreach($players as $player) {
            foreach($player->getTeam() as $team) {
                if($team->getValidated() === Participant::STATUS_VALIDATED) {
                    $out[] = $player;
                    break;
                }
            }
        }

        return $out;
    }

}
