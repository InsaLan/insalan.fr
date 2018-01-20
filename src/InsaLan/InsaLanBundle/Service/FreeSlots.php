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
use InsaLan\TournamentBundle\Entity\Participant;

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
                                              ->findByValidated(Participant::STATUS_VALIDATED));
    }

    /**
     * @return Participant The next participant to send to validated state, or null if not available.
     */
    public function selectWaitingTeam()
    {
        $participant = $this->doctrine
                            ->getRepository('InsaLanTournamentBundle:Participant')
                            ->findBy(
                                array("validated" => Participant::STATUS_WAITING), //select
                                array("id"        => "ASC"),                       //order by
                                1
                            );                                                //limit
        if (count($participant) === 1) {
            return $participant[0];
        } else {
            return null;
        }
    }

    public function opened()
    {
        $date = strtotime(\InsaLan\InformationBundle\Controller\DefaultController::OPENING_DATE);
        return (time() >= $date);
    }
}
