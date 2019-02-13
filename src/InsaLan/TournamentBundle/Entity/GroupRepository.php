<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use InsaLan\TournamentBundle\Entity;

class GroupRepository extends EntityRepository
{
    protected function getQueryBuilder($gs = true)
    {
        $qb = $this->_em->createQueryBuilder()
            ->from($this->_entityName, 'g')
            ->leftJoin('g.participants', 'p')
            ->leftJoin('g.matches', 'm')
            ->leftJoin('m.part1', 'p1')
            ->leftJoin('m.part2', 'p2')
            ->leftJoin('m.rounds', 'r')
            ->leftJoin('m.scores', 'sc')
            ->addSelect('partial g.{id, name, statsType}')
            ->addSelect('partial m.{id, state}')
            ->addSelect('partial r.{id, replay}')
            ->addSelect('sc')
            ->addSelect('p')
            ->addSelect('p1')
            ->addSelect('p2');

        if ($gs) {
          $qb
            ->leftJoin('g.stage', 'gs')
            ->addSelect('partial gs.{id, name}')
            ->orderBy('gs.name, g.name');
        }

        return $qb;
    }

    public function getByStages(Array $stages)
    {
        $q = $this->getQueryBuilder();
        $s = array();
        foreach ($stages as $stage) {
            $s[] = $stage->getId();
        }

        $q->where('g.stage IN (:s)')->setParameter('s', implode(',', $s));
        $q->orderBy('g.stage');
        return $q->getQuery()->execute();
    }

    public function getByStage(Entity\GroupStage $s)
    {
        $q = $this->getQueryBuilder();
        $q->where('g.stage = :s')->setParameter('s', $s);

        return $q->getQuery()->execute();
    }

    public function getById($id, $gs = true)
    {
        $q = $this->getQueryBuilder($gs);
        $q->where('g.id = :i')->setParameter('i', (int)$id);

        return $q->getQuery()->getSingleResult();
    }

    public function getByTournament(Entity\Tournament $t)
    {
        $q = $this->getQueryBuilder();
        $q->where('gs.tournament = :t')->setParameter('t', $t);

        return $q->getQuery()->execute();
    }
}
