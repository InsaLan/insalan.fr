<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use InsaLan\TournamentBundle\Entity;

class RoyalMatchRepository extends EntityRepository
{
    protected function getQueryBuilder()
    {
        return $this->_em->createQueryBuilder()
            ->from($this->_entityName, 'm')
            ->leftJoin('m.participants', 'p')
            ->leftJoin('m.rounds', 'r')
            ->leftJoin('m.koMatch', 'k')
            ->addSelect('k')
            ->addSelect('partial m.{id, state}')
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
