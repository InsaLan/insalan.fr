<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use InsaLan\TournamentBundle\Entity;

class GroupRepository extends EntityRepository
{
    protected function getQueryBuilder()
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.matches', 'gm')
            ->leftJoin('g.stage', 'gs')
            ->leftJoin('gm.match', 'm')
            ->leftJoin('m.rounds', 'r')
            ->leftJoin('m.part1', 'p1')
            ->leftJoin('m.part2', 'p2')
            ->addSelect('m')
            ->addSelect('gm')
            ->addSelect('gs')
            ->addSelect('r')
            ->addSelect('p1')
            ->addSelect('p2')
        ;
    }
    public function getByStage(Entity\GroupStage $s)
    {
        $q = $this->getQueryBuilder();
        $q->where('g.stage = :s')->setParameter('s', $s);

        return $q->getQuery()->execute();
    }
}
