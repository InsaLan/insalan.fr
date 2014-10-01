<?php

/**
 * It probably needs code review.
 *
 * Affects app/config/config.yml with a twig global variable.
 *
 * @author Lesterpig
 */

namespace InsaLan\InsaLanBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;

class FreeSlots
{ 
    const TOTAL_SLOTS = 64;

    protected $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return int Number of free slots currently available.
     */
    public function get()
    {
       return self::TOTAL_SLOTS - count($this->doctrine
                                             ->getRepository('InsaLanTournamentBundle:Participant')
                                             ->findByValidated(true));
    }
}
?>