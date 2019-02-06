<?php

/**
 * By Lesterpig ~ 01/2015
 * This repository is able to manage simple & double elimination based tournaments,
 * with a undefined number of users.
 *
 * Not yet really stable... /!\
 *
 * contact@lesterpig.com for more details
 */

namespace InsaLan\TournamentBundle\Entity;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class KnockoutMatchRepository extends NestedTreeRepository
{   

    /**
     * Generate a correct sized tree based on players number.
     * @param  Knockout $knockout
     * @param  Number   $players
     * @param  Boolean  $doubleElimination
     * @return KnockoutMatch The root of the tree
     */
    public function generateMatches(Knockout $knockout, $players, $doubleElimination = false) {
        $players = intval($players);
        if($players <= 1)
            throw new \Exception("Wrong number of players while generating a KO tree");

        $lvl = 1;
        while(pow(2, $lvl) < $players) { $lvl++; }
        
        $em = $this->getEntityManager();

        $root = new KnockoutMatch();
        $root->setKnockout($knockout);

        $this->createChildren($root, $lvl - 1);

        if($doubleElimination) {

            $winnerRoot = $root;
            $knockout->setDoubleElimination(true);
            $loserRoot = new KnockoutMatch();
            $loserRoot->setKnockout($knockout);

            $this->createLoserChildren($loserRoot, 2 * ($lvl - 1) - 1, true);

            $globalRoot = new KnockoutMatch();
            $globalRoot->setKnockout($knockout);
            $this->addChild($globalRoot, $winnerRoot);
            $this->addChild($globalRoot, $loserRoot);


            $em->persist($globalRoot);
            $em->persist($winnerRoot);
            $em->persist($loserRoot);
            $em->flush();

            /**
             * Links between winning and losing matches
             * We are iterating in the two segments of the tree simultaneously.
             */

            $i = $lvl;
            $j = 2*($lvl-1);
            $order = "asc";

            $this->linkMatches($winnerRoot, $loserRoot, $i, $j, $order, true);
            $i--; $j--;

            while($i > 0) {

                $order = ($order === "asc" ? "desc" : "asc");
                $this->linkMatches($winnerRoot, $loserRoot, $i, $j, $order);
                $i--;
                $j-=2;
                
            }
            
            $root = $globalRoot;
        } else {
            $em->persist($root);
        }
        
        $em->flush();

        return $root;
    }

    /**
     * Propagate each knockoutMatch in a Knockout
     * It handles correctly null participants and absent matches
     * 
     * @param  Knockout $ko
     */
    public function propagateVictoryAll(Knockout $ko) {
        $globalRoot = $this->getRoot($ko);
        $this->propagateFromNode($globalRoot);
    }

    /**
     * Propagate a knockoutMatch and its children
     * 
     * @param  KnockoutMatch $koMatch
     */
    public function propagateFromNode(KnockoutMatch $koMatch) {


        $children = $koMatch->getChildren()->toArray();
        $allChildrenEnded = true;

        foreach($children as $child) {
            $this->propagateFromNode($child);
            $allChildrenEnded  &= $child->getMatch() !== null
                                && $child->getMatch()->getState() === Match::STATE_FINISHED;
        }

        //echo("Propagating " . $koMatch->__toString() . " : ");

        if($allChildrenEnded && // if children are ok
            (!$koMatch->getOddNode() || $koMatch->cancelWait || // we are not waiting for somebody in winner bracket
                (sizeof($koMatch->getChildren()) > 0 && $koMatch->getMatch() && $koMatch->getMatch()->getPart2()))) {

            //echo " (candidate) ";

            // All children are propagated and we are not waiting for another team.
            // Let's check if this is not an empty match
            if(!($match = $koMatch->getMatch())) {
                $match = new Match();
                $match->setKoMatch($koMatch);
                $koMatch->setMatch($match);
            }

            //echo $match->getState() . " ";

            if($match->getState() !== Match::STATE_FINISHED) {
                if(!$match->getPart2()) {
                    $this->setVictoryForPart1($match);
                    //echo("Victory is for part2");
                }
                elseif(!$match->getPart1()) {
                    $this->setVictoryForPart2($match);
                    //echo("Victory is for part1");
                }
                $this->getEntityManager()->persist($koMatch);
            }

            $loserKo = $koMatch->getLoserDestination();
            if($loserKo && (!$match->getPart1() || !$match->getPart2())) {
                $loserKo->cancelWait = true;
                $loserKo->setOddNode(false);
                $this->getEntityManager()->persist($loserKo);
            }

        }

        //echo("\n");

        $this->propagateVictory($koMatch);
    }

    /**
     * Propagate winner into the tree to the immediate parent
     * and to the associated loser bracket match
     * 
     * @param  KnockoutMatch $koMatch
     */
    public function propagateVictory(KnockoutMatch $koMatch) {   
        $em = $this->getEntityManager();
        $match  = $koMatch->getMatch();
        $parent = $koMatch->getParent();
        $first  = true;

        // Determines wether this match is the first or the second
            
        $children = $this->getChildren($parent, true, 'left');

        foreach($children as $i=>$child) {
            if($i === 0 && $child !== $koMatch)
                $first = false;
        }

        if($match === null || $match->getState() !== Match::STATE_FINISHED || $parent === null)
            return;

        $winner = $match->getWinner();
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

        /** 
         * Loser management if needed
         */
        
        if(!$koMatch->getKnockout()->getDoubleElimination()) return;

        $loser      = $match->getLoser();
        $loserKo    = $koMatch->getLoserDestination();
        
        if(!$loserKo) return;

        $loserMatch = $loserKo->getMatch();

        if(!$loserMatch) {
            $loserMatch = new Match();
            $loserMatch->setKoMatch($loserKo);
            $loserKo->setMatch($loserMatch);
        }

        if($loserKo->getChildren()->isEmpty()) {
            if($first)
                $loserMatch->setPart1($loser);
            else
                $loserMatch->setPart2($loser);
        } else
            $loserMatch->setPart2($loser);

        $em->persist($loserMatch);
        $em->persist($loserKo);

    }

    /**
     * Get the correct KOMatch root (final) given a Knockout tree.
     * @param  Knockout $ko The knockout container
     * @return KnockoutMatch
     */
    public function getRoot(Knockout $ko) {
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
    public function getDepth(Knockout $ko) {
        $q = $this->createQueryBuilder('kom')
            ->select('MAX(kom.level)')
            ->where('kom.knockout = :k')
            ->setParameter('k', $ko);

        return intval($q->getQuery()->getSingleScalarResult());
    }

    public function getLeftDepth(Knockout $ko) {
        $element  = $this->getRoot($ko);
        $children = $element->getChildren();
        while(!$children->isEmpty()) {
            $element = $children->get(0);
            $children = $element->getChildren();
        }

        return $element->getLevel();
    }

    /**
     * Get children that are at a specific level, sorted from left to right
     * @param  Knockout $ko  The knockout tree
     * @param  Number   $lvl 
     * @return KnockoutMatch[]
     */
    public function getLvlChildren(KnockoutMatch $root, $lvl, $sortOrder = "asc") {   

        if($root->getLevel() === $lvl) return array($root);

        $children = $this->getChildren($root, null, "left", $sortOrder);

        $outChildren = array();
        foreach($children as $child)
        {
            if($child->getLevel() === $lvl)
                $outChildren[] = $child;
        }

        return $outChildren;
    }

    /**
     * Get JSON format for jQuery Bracket
     * @param  Knockout $ko
     * @return String
     */
    public function getJson(Knockout $ko) {
        $teams = array();
        $depth = $this->getLeftDepth($ko);

        $globalRoot = $this->getRoot($ko);

        $root  = $ko->getDoubleElimination() ? $globalRoot->getChildren()->get(0) : $globalRoot;
        $children = $this->getLvlChildren($root, $depth);

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

        for($lvl = $depth; $lvl >= ($ko->getDoubleElimination() ? 1 : 0); $lvl--) {
            $parents = array();
            $round   = array();
            $i = 0;
            foreach($children as $child) {

                if($i++ % 2 === 0)
                    $parents[] = $child->getParent();

                $round[] = $this->getMatchJson($child);

            }
            $results[] = $round;
            $children = $parents;
        }

        if(!$ko->getDoubleElimination())

            return json_encode(array(
                "teams" => $teams,
                "results" => $results
            ));

        $losers = array();
        $root  = $globalRoot->getChildren()->get(1);
        $depth = $this->getDepth($ko);
        $children = $this->getLvlChildren($root, $depth);

        for($lvl = $depth; $lvl >= 1; $lvl--) {
            $parents = array();
            $round   = array();
            $i = 0;
            foreach($children as $child) {
                $parent = $child->getParent();
                if($parent && !in_array($parent, $parents))
                    $parents[] = $parent;
                $round[] = $this->getMatchJson($child);
            }
            $losers[] = $round;
            $children = $parents;
        }

        $finals = array($this->getMatchJson($globalRoot));

        return json_encode(array(
                "teams" => $teams,
                "results" => array($results, $losers, array($finals))
        ));

    }


    /***** PRIVATE ******/

    private function getMatchJson($child) {
        $match = $child->getMatch();
        if($match === null) {
            return array(null, null);
        }

        return array($match->getScore1(), $match->getScore2());
    }

    private function createChildren(KnockoutMatch $koMatch, $depth) {
        if($depth <= 0) return;

        for($i = 0; $i < 2; $i++) {
            $child = $this->addChild($koMatch);
            $this->createChildren($child, $depth - 1);
            $this->getEntityManager()->persist($child);
        }

    }

    private function createLoserChildren(KnockoutMatch $koMatch, $depth, $importLoser) {
        if($depth <= 0) {
            $koMatch->setOddNode(true);
            return;
        }

        for($i = 0; $i < ($importLoser ? 1 : 2); $i++) {
            $child = $this->addChild($koMatch);
            $this->createLoserChildren($child, $depth - 1, !$importLoser);
            $this->getEntityManager()->persist($child);
        }

        if($importLoser)
            $koMatch->setOddNode(true);
    }

    private function addChild(KnockoutMatch $parent, KnockoutMatch $child = null) {
        if(!$child)
            $child = new KnockoutMatch();

        $child->setKnockout($parent->getKnockout());
        $child->setParent($parent);
        $parent->addChildren($child);

        return $child;
    }

    private function linkMatches($winRoot, $losRoot, $winLvl, $losLvl, $order, $first = false) {

        $winMatches = $this->getLvlChildren($winRoot, $winLvl, $order);
        $losMatches = $this->getLvlChildren($losRoot, $losLvl, "asc");

        $i = 0;
        foreach($losMatches as $losMatch) {
            for($j = 0; $j < ($first ? 2 : 1); $j++) {
                $winMatches[$i]->setLoserDestination($losMatch);
                $this->getEntityManager()->persist($winMatches[$i++]);
            }
        }

    }

    private function setVictoryForPart1(Match $match) {
        $r = new Round();
        $r->setScore($match->getPart1(), 1);
        $r->setScore($match->getPart2(), 0);
        $r->setMatch($match);
        $match->addRound($r);
        $match->setState(Match::STATE_FINISHED);
        $this->getEntityManager()->persist($r);
        $this->getEntityManager()->persist($match);
    }

    private function setVictoryForPart2(Match $match) {
        $r = new Round();
        $r->setScore($match->getPart1(), 0);
        $r->setScore($match->getPart2(), 1);
        $r->setMatch($match);
        $match->addRound($r);
        $match->setState(Match::STATE_FINISHED);
        $this->getEntityManager()->persist($r);
        $this->getEntityManager()->persist($match);
    }

}