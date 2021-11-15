<?php
namespace App\Entity;

use Doctrine\ORM\EntityRepository;
use App\Entity;

class TournamentMatchRepository extends EntityRepository
{
    protected function getQueryBuilder()
    {
        return $this->_em->createQueryBuilder()
            ->from($this->_entityName, 'm')
            ->leftJoin('m.part1', 'p1')
            ->leftJoin('m.part2', 'p2')
            ->leftJoin('p1.manager', 'ma1')
            ->leftJoin('p2.manager', 'ma2')
            ->leftJoin('m.rounds', 'r')
            ->leftJoin('m.koMatch', 'k')
            ->addSelect('k')
            ->addSelect('partial m.{id, state}')
            ->addSelect('p1')
            ->addSelect('p2')
            ->addSelect('ma1')
            ->addSelect('ma2')
        ;
    }

    public function getByGroup($id)
    {
        $q = $this->getQueryBuilder();
        $q->addSelect('partial r.{id, replay}');
        $q->leftJoin('r.scores', 'sc');
        $q->addSelect('sc');

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

    public function getByParticipant(Entity\Participant $part)
    {
        $q = $this->getQueryBuilder();
        $q->addSelect('partial r.{id, replay}');
        $q->leftJoin('r.scores', 'sc');
        $q->addSelect('sc');
        $q->where('m.part1 = :a OR m.part2 = :a')
          ->setParameter('a', $part);
        $q->orderBy("m.id");

        return $q->getQuery()->execute();
    }

    public function getByGroupStage(Entity\TournamentGroupStage $stage)
    {
        $q = $this->getQueryBuilder()
        ->leftJoin('m.group', 'g')
        ->leftJoin('g.stage', 's')
        ->addSelect('g')
        ->addSelect('r')
        ->where('s = :stage')
        ->setParameter('stage', $stage)
        ->orderBy("g.id");

        return $q->getQuery()->execute();
    }

    public function getByKnockout(Entity\TournamentKnockout $ko)
    {
        $q = $this->getQueryBuilder()
        ->leftJoin('m.koMatch', 'kom')
        ->leftJoin('kom.knockout', 'ko')
        ->addSelect('kom')
        ->addSelect('r')
        ->where('ko = :k')
        ->setParameter('k', $ko)
        ->orderBy("kom.level");

        return $q->getQuery()->execute();
    }

    public function getByTournament(Entity\Tournament $t)
    {
        $q = $this->getQueryBuilder()
        ->leftJoin('m.koMatch', 'kom')
        ->leftJoin('kom.knockout', 'ko')
        ->leftJoin('ko.tournament', 't')
        ->addSelect('ko')
        ->addSelect('kom')
        ->addSelect('r')
        ->where('t = :tournament')
        ->setParameter('tournament', $t);

        return $q->getQuery()->execute();
    }
}
