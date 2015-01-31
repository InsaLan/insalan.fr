<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use InsaLan\TournamentBundle\Entity;

class KnockoutRepository extends EntityRepository
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
            ->leftJoin('m.rounds', 'r')
            ->addSelect('partial t.{id,name,participantType}')
            ->addSelect('ko')
            ->addSelect('kom')
            ->addSelect('m')
            ->addSelect('r')
            ->addSelect('p1')
            ->addSelect('p2')
        ;
    }

    public function getByTournament(Entity\Tournament $t)
    {
        $q = $this->getQueryBuilder();
        $q->where('t = :t')->setParameter('t', $t);
        return $q->getQuery()->execute();
    }
}
