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
            $parentMatch->setKoMatch($parent);
            $parent->setMatch($parentMatch);
        }

        if($parentMatch->getPart1() === null)
            $parentMatch->setPart1($winner);
        elseif($parentMatch->getPart2() === null && $parentMatch->getPart1()->getId() !== $winner->getId())
            $parentMatch->setPart2($winner);
        else
            throw new \Exception("Unexpected invalid tree propagation : a match has already been created and filled with participants !");

        $em = $this->getEntityManager();
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

        return $q->getQuery()->getSingleScalarResult();
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