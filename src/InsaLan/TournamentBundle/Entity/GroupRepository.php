<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use InsaLan\TournamentBundle\Entity;

class GroupRepository extends EntityRepository
{
    protected function getQueryBuilder()
    {
        return $this->_em->createQueryBuilder()
            ->from($this->_entityName, 'g')
            ->leftJoin('g.stage', 'gs')
//            ->leftJoin('g.participants', 'gp')
            ->leftJoin('g.participants', 'p')
//            ->leftJoin('g.matches', 'gm')
            ->addSelect('partial g.{id, name}')
            ->addSelect('partial gs.{id, name}')
//            ->addSelect('gp')
            ->addSelect('p')
            ->orderBy('gs.name, g.name')
        ;
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

    public function getById($id)
    {
        $q = $this->getQueryBuilder();
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
