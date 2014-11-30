<?php

/** TODO add tests **/

namespace InsaLan\TournamentBundle\Entity;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class KnockoutMatchRepository extends NestedTreeRepository
{

    /**
     * Generate a correct sized tree based on players number.
     * @param  Knockout $knockout
     * @param  Number   $players
     * @return KnockoutMatch The root of the tree
     */
    public function generateMatches(Knockout $knockout, $players)
    {
        $players = intval($players);
        if($players <= 1)
            throw new \Exception("Wrong number of players while generating a KO tree");

        $lvl = 1;
        while(pow(2, $lvl) < $players) { $lvl++; }
        
        $em = $this->getEntityManager();

        $root = new KnockoutMatch();
        $root->setKnockout($knockout);

        $this->createChildren($root, $lvl - 1);

        $em->persist($root);
        $em->flush();

        return $root;
    }

    /**
     * Propagate winner into the tree
     * @param  KnockoutMatch $koMatch
     * @param  Boolean       $forcePropagate allow propagate if match not played
     */
    public function propagateVictory(KnockoutMatch $koMatch, $forcePropagate = false)
    {   
        $em = $this->getEntityManager();
        $match  = $koMatch->getMatch();
        $parent = $koMatch->getParent();
        $first  = true;

        // Determines wether this match is the first or the second
            
        $children = $this->getChildren($parent, true, 'left');
        if($children[0]->getId() !== $koMatch->getId())
            $first = false;


        if($match === null)
            return;

        if($match->getState() !== Match::STATE_FINISHED && !$forcePropagate)
            return;

        if($parent === null)
            return; /** TODO : call a kind of "endOfKnockout" method **/

        $winner = $match->getWinner();

        // If autopropagated this case can happen
        if($match->getPart2() === null) {
            $match->setState(Match::STATE_FINISHED);
            $r = new Round();
            $r->setScore1(1);
            $r->setScore2(0);
            $r->setMatch($match);
            $match->addRound($r);
            $em->persist($r);
        }

        $parentMatch = $parent->getMatch();

        if($parentMatch === null) {
            $parentMatch = new Match();
            $parentMatch->setKoMatch($parent);
            $parent->setMatch($parentMatch);
        }

        if($first)
            $parentMatch->setPart1($winner);
        else
            $parentMatch->setPart2($winner);
    
        $em->persist($parentMatch);
        $em->persist($parent);
        $em->flush();

        /** TODO : loser bracket listenner!  **/

    }

    /**
     * Get the correct KOMatch root (final) given a Knockout tree.
     * @param  Knockout $ko The knockout container
     * @return KnockoutMatch
     */
    public function getRoot(Knockout $ko)
    {
        $q = $this->createQueryBuilder('kom')
            ->where('kom.knockout = :k AND kom.parent IS NULL')
            ->setParameter('k', $ko);

        return $q->getQuery()->getSingleResult();
    }

    /**
     * Get the depth of a given tree from 0.
     * For example :
     *
     * X
     * X  X       
     *      X
     * X  X
     * X  
     *
     * Has a depth of 2
     *
     * @param  Knockout $ko [description]
     * @return [type]       [description]
     */
    public function getDepth(Knockout $ko)
    {
        $q = $this->createQueryBuilder('kom')
            ->select('MAX(kom.level)')
            ->where('kom.knockout = :k')
            ->setParameter('k', $ko);

        return intval($q->getQuery()->getSingleScalarResult());
    }

    /**
     * Get children that are at a specific level, sorted from left to right
     * @param  Knockout $ko  The knockout tree
     * @param  Number   $lvl 
     * @return KnockoutMatch[]
     */
    public function getLvlChildren(Knockout $ko, $lvl)
    {   

        

        $children = $this->getChildren($this->getRoot($ko), null, "left", "asc");

        $lastChildren = array();
        foreach($children as $child)
        {
            if($child->getLevel() === $lvl)
                $lastChildren[] = $child;
        }

        return $lastChildren;
    }

    /**
     * Get JSON format for jQuery Bracket
     * @param  Knockout $ko
     * @return String
     */
    public function getJson(Knockout $ko)
    {
        $teams = array();
        $depth = $this->getDepth($ko);

        $children = $this->getLvlChildren($ko, $depth);

        foreach($children as $child) {

            $match = $child->getMatch();
            if($match === null) {
                $teams[] = array("-", "-");
                continue;
            }

            $part1 = $match->getPart1();
            $part2 = $match->getPart2();

            if($part1 === null) $part1 = "-";
            else $part1 = $part1->getName();

            if($part2 === null) $part2 = "-";
            else $part2 = $part2->getName();

            $teams[] = array($part1, $part2);
        }

        $results = array();

        for($lvl = $depth; $lvl >= 0; $lvl--) {
            $parents = array();
            $round   = array();
            $i = 0;
            foreach($children as $child) {
                
                if($i++ % 2 === 0)
                    $parents[] = $child->getParent();

                $match = $child->getMatch();
                if($match === null) {
                    $round[] = array(null, null);
                    continue;
                }

                $round[] = array($match->getScore1(), $match->getScore2());

            }
            $results[] = $round;
            $children = $parents;
        }

        return json_encode(array(
            "teams" => $teams,
            "results" => $results
        ));

    }


    /***** PRIVATE ******/

    private function createChildren(KnockoutMatch $koMatch, $depth)
    {
        if($depth <= 0) return;

        for($i = 0; $i < 2; $i++)
        {
            $child = new KnockoutMatch();
            $child->setKnockout($koMatch->getKnockout());
            $child->setParent($koMatch);
            $koMatch->addChildren($child);

            $this->createChildren($child, $depth - 1);

            $this->getEntityManager()->persist($child);
        }

    }

}