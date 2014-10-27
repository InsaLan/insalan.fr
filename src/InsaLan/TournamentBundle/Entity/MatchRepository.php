<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use InsaLan\TournamentBundle\Entity;

class MatchRepository extends EntityRepository
{
    protected function getQueryBuilder()
    {
        return $this->_em->createQueryBuilder()
            ->from($this->_entityName, 'm')
            ->leftJoin('m.part1', 'p1')
            ->leftJoin('m.part2', 'p2')
            ->leftJoin('m.rounds', 'r')
            ->addSelect('partial m.{id, state}')
            ->addSelect('p1')
            ->addSelect('p2')
        ;
    }

    public function getByGroup($id)
    {
        $q = $this->getQueryBuilder();
        $q->addSelect('partial r.{id, score1, score2, replay}');

        $q->where('m.group = :g')->setParameter('g', (int)$id);
        return $q->getQuery()->execute();
    }

    public function getById($id)
    {
        $q = $this->getQueryBuilder();
        $q->leftJoin('m.group', 'g');
        $q->addSelect('g');
        $q->addSelect('r');
        $q->where('m.id = :id')->setParameter('id', (int)$id);
        return $q->getQuery()->getSingleResult();
    }
}
