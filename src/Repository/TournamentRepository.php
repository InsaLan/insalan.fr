<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

use App\Entity\Participant;

class TournamentRepository extends EntityRepository
{
    public function findThisYearTournaments($time) {
        $query = $this->createQueryBuilder('t')
            ->leftJoin('t.participants', 'p')
            ->addSelect('p')
            ->leftJoin('p.manager', 'm')
            ->addSelect('partial m.{id}') // TODO : find why doctrine needs to populate managers...
            ->where('t.registrationOpen >= :lastyear')
            ->setParameter('lastyear', (new \DateTime('now'))->modify('-' . $time . ' month'))
            ->orderBy('t.tournamentOpen', 'ASC')
            ->getQuery();
        return $query->getResult();
    }

    public function findOpened() {
        $query = $this->createQueryBuilder('t')
            ->where('t.registrationOpen <= :now AND t.registrationClose >= :now')
            ->setParameter('now', new \DateTime())
            ->getQuery();
        return $query->getResult();
    }

    public function findPlaying() {
        $query = $this->createQueryBuilder('t')
            ->where('t.tournamentOpen <= :now AND t.tournamentClose >= :now')
            ->setParameter('now', new \DateTime())
            ->getQuery();
        return $query->getResult();
    }
    public function getFreeSlots($tournamentId) {
        $tournament = $this->findOneById($tournamentId);
        $freeSlots = $tournament->getRegistrationLimit();
        foreach($tournament->getParticipants() as $participant) {
            if ($participant->getValidated() === Participant::STATUS_VALIDATED) {
                $freeSlots--;
            }
        }
        return $freeSlots;
    }

    public function selectWaitingParticipant($tournamentId) {
        $tournament = $this->findOneById($tournamentId);
        $participants = array();
        foreach($tournament->getParticipants() as $participant) {
            if ($participant->getValidated() === Participant::STATUS_WAITING) {
                $participants[] = $participant;
            }
        }
        return $participants;
    }

    /**
     * Get an associative array of unavailable placements for fast determination.
     * Only keys where placement is unavailable are set, because we cannot determine
     * the number of places.
     *
     * @param  \App\Entity\Tournament $t
     * @return Associative array (integer=>true)
     */
    public function getUnavailablePlacements(\App\Entity\Tournament $t) {
        $em = $this->getEntityManager();
        $res = $em->createQuery("
            SELECT DISTINCT p.placement FROM \App\Entity\Participant p
            WHERE p.placement IS NOT NULL AND p.tournament = :t")
                ->setParameter("t", $t)
                ->getResult();

        $out = array();
        foreach($res as $p) {
            $out[$p["placement"]] = true;
        }
        return $out;
    }

    public function findPreviousYearTournaments($year) {
        $query = $this->createQueryBuilder('t')
            ->leftJoin('t.participants', 'p')
            ->addSelect('p')
            ->leftJoin('p.manager', 'm')
            ->addSelect('partial m.{id}') // TODO : find why doctrine needs to populate managers...
            ->Where('t.tournamentOpen BETWEEN :start AND :end')
            ->setParameter('start', new \Datetime($year.'-01-01'))
            ->setParameter('end',   new \Datetime($year.'-05-31'))
            ->orderBy('t.tournamentOpen', 'ASC')
            ->getQuery();
        return $query->getResult();
    }

    public function getUpcomingTournaments() {
        $query = $this->createQueryBuilder('t')
            ->Where('t.tournamentClose >= :date')
            ->setParameter('date', new \Datetime("now"))
            ->getQuery();
        return $query->getResult();

    }
}
