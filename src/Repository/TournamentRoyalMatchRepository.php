<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity;

class TournamentRoyalMatchRepository extends EntityRepository
{
    protected function getQueryBuilder()
    {
        return $this->_em->createQueryBuilder()
            ->from($this->_entityName, 'm')
            ->leftJoin('m.participants', 'p')
            ->leftJoin('m.participants', 'p2')
            ->leftJoin('m.rounds', 'r')
            ->leftJoin('m.koMatch', 'k')
            ->leftJoin('p2.manager', 'ma')
            ->addSelect('k')
            ->addSelect('partial m.{id, state}')
            ->addSelect('p2')
            ->addSelect('ma')
        ;
    }

    public function getByParticipant(Entity\Participant $part)
    {
        $q = $this->getQueryBuilder();
        $q->addSelect('partial r.{id, replay}');
        $q->leftJoin('r.scores', 'sc');
        $q->addSelect('sc');
        $q->where('p = :a')
          ->setParameter('a', $part);
        $q->orderBy("m.id");

        return $q->getQuery()->getResult();
    }
}
