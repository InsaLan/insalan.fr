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
            ->leftJoin('m.rounds', 'r')
            ->leftJoin('p.manager', 'ma')
            ->leftJoin('r.scores', 'sc')
            ->addSelect('partial g.{id, name, statsType}')
            ->addSelect('partial gs.{id, name}')
            ->addSelect('partial m.{id, state}')
            ->addSelect('partial r.{id, replay}')
            ->addSelect('p')
            ->addSelect('ma')
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
