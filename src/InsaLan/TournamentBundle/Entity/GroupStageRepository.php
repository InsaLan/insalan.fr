<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use InsaLan\TournamentBundle\Entity;

class GroupStageRepository extends EntityRepository
{
    protected function getQueryBuilder()
    {
        return $this->_em->createQueryBuilder()
            ->from($this->_entityName, 'gs')
            ->leftJoin('gs.groups', 'g')
            ->leftJoin('g.participants', 'p')
            ->leftJoin('g.matches', 'm')
            ->leftJoin('m.part1', 'p1')
            ->leftJoin('m.part2', 'p2')
            ->leftJoin('m.rounds', 'r')
            ->leftJoin('p1.manager', 'ma1')
            ->leftJoin('p2.manager', 'ma2')
            ->leftJoin('r.scores', 'sc')
            ->addSelect('partial g.{id, name}')
            ->addSelect('partial gs.{id, name}')
            ->addSelect('partial m.{id, state}')
            ->addSelect('partial r.{id, replay}')
            ->addSelect('p')
            ->addSelect('p1')
            ->addSelect('p2')
            ->addSelect('ma1')
            ->addSelect('ma2')
            ->addSelect('sc')
            ->orderBy('gs.name, g.name, p.id')
        ;
    }

    public function getByTournament(Entity\Tournament $t)
    {
        $q = $this->getQueryBuilder();
        $q->where('gs.tournament = :t')->setParameter('t', $t);
        return $q->getQuery()->execute();
    }
}
