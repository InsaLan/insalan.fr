<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use App\Entity\Tournament;

class TournamentKnockoutRepository extends EntityRepository
{
    protected function getQueryBuilder()
    {
        return $this->_em->createQueryBuilder()
            ->from($this->_entityName, 'ko')
            ->leftJoin('ko.tournament', 't')
            ->leftJoin('ko.matches', 'kom')
            ->leftJoin('kom.match', 'm')
            ->leftJoin('m.part1', 'p1')
            ->leftJoin('m.part2', 'p2')
            ->leftJoin('p1.manager', 'ma1')
            ->leftJoin('p2.manager', 'ma2')
            ->leftJoin('m.rounds', 'r')
            ->addSelect('partial t.{id,name,participantType}')
            ->addSelect('ko')
            ->addSelect('kom')
            ->addSelect('m')
            ->addSelect('r')
            ->addSelect('p1')
            ->addSelect('p2')
            ->addSelect('ma1')
            ->addSelect('ma2')
        ;
    }

    public function getByTournament(Tournament $t)
    {
        $q = $this->getQueryBuilder();
        $q->where('t = :t')->setParameter('t', $t);
        return $q->getQuery()->execute();
    }
}
