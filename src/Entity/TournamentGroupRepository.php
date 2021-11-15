<?php
namespace App\Entity;

use Doctrine\ORM\EntityRepository;
use App\Entity;

class TournamentGroupRepository extends EntityRepository
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

    public function getByStage(Entity\TournamentGroupStage $s)
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

    public function autoManageMatches(Entity\Group $group)
    {

        $em = $this->getEntityManager();

        if ($group->getStatsType() == Group::STATS_WINLOST) {
            // Clean up deprecated matches

            foreach($group->getMatches()->toArray() as $match) {

                foreach($match->getParticipants() as $p) {
                    if (!$group->hasParticipant($p)) {

                        $group->removeMatch($match);
                        $em->remove($match);

                        break;
                    }
                }
            }

            // Create missing matches

            $participants = $group->getParticipants()->getValues();

            for($i = 0; $i < count($participants); $i++)
            {
                for($j = $i+1; $j < count($participants); $j++)
                {

                    $a = $participants[$i];
                    $b = $participants[$j];

                    if(!$group->getMatchBetween($a, $b)) {
                        $match = new Match();
                        $match->setPart1($a);
                        $match->setPart2($b);
                        $match->setState(Match::STATE_UPCOMING);
                        $match->setGroup($group);
                        $group->addMatch($match);
                        $em->persist($match);
                    }

                }
            }
        }
        else {
            // assume STATS_SCORE groups only have 1 RoyalMatch with every participants => battle royale tournaments

            // create match if missing
            if ($group->getMatches()->count() == 0) {
                $m = new RoyalMatch();
                $m->setState(Match::STATE_UPCOMING);
                $m->setGroup($group);
                $group->addMatch($m);
            }

            $match = $group->getMatches()->first();

            // set match participants to all group participants
            foreach($match->getParticipants() as $p) {
                if (!$group->hasParticipant($p)) {
                    $match->removeParticipant($p);
                }
            }

            foreach($group->getParticipants() as $p) {
                if (!$match->hasParticipant($p)) {
                    $match->addParticipant($p);
                }
            }

            $em->persist($match);
        }

    }
}
