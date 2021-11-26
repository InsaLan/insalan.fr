<?php

/**
 * It probably needs code review.
 *
 * Affects app/config/config.yml with a twig global variable.
 *
 * @author Lesterpig
 */

namespace App\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use App\Entity\Participant;

class InsaLanFreeSlots
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
                                              ->getRepository('App\Entity\Participant')
                                              ->findByValidated(Participant::STATUS_VALIDATED));
    }

    /**
     * @return Participant The next participant to send to validated state, or null if not available.
     */
    public function selectWaitingTeam() {
        $participant = $this->doctrine
                            ->getRepository('App\Entity\Participant')
                            ->findBy(array("validated" => Participant::STATUS_WAITING), //select
                                     array("id"        => "ASC"),                       //order by
                                     1);                                                //limit
        if(count($participant) === 1) return $participant[0];
        else return null;
    }

    public function opened()
    {
        $date = strtotime(\App\Controller\InsaLanController::OPENING_DATE);
        return (time() >= $date);
    }
}
?>