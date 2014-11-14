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
     */
    public function generateMatches(Knockout $knockout, $players)
    {
        $players = intval($players);
        if($players <= 1)
            throw new \Exception("Wrong number of players while generating a KO tree");

        $lvl = 1;
        while(pow(2, $lvl) <= $players) { $lvl++; }
        
        $em = $this->getEntityManager();

        $root = new KnockoutMatch();
        $root->setKnockout($knockout);

        createChildren($root, $lvl - 2);

        $em->persist($root);
        $em->flush();
    }

    /**
     * Propagate winner into the tree
     * @param  KnockoutMatch $koMatch
     */
    public function propagateVictory(KnockoutMatch $koMatch)
    {
        $match  = $koMatch->getMatch();
        $parent = $koMatch->getParent();

        if($match === null)
            throw new \Exception("Tried to propagate an uninitialized match");

        if($match->getState() !== Match::STATE_FINISHED)
            return;

        if($parent === null)
            return; /** TODO : call a kind of "endOfKnockout" method **/

        $winner      = $match->getWinner();
        $parentMatch = $parent->getMatch();

        if($winner === null)
            return; /** TODO : throw a error to the correct handler, because we need to make a decision. This case shoul never happen. **/

        if($parentMatch === null) {
            $parentMatch = new Match();
            $parent->setMatch($parentMatch);
        }

        if($parentMatch->getPart1() === null)
            $parentMatch->setPart1($winner);
        elseif($parentMatch->getPart2() === null && $parentMatch->getPart1()->getId() !== $winner->getId())
            $parentMatch->setPart2($winner);
        else
            throw new \Exception("Unexpected invalid tree propagation : a match has already been created and filled with participants !");

        /** TODO : loser bracket listenner!  **/

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

            $this->createChildren($child, $depth - 1);

            $this->getEntityManager()->persist($child);
        }

    }

}