<?php

/**
 * The service for player placement structure handling.
 * It's very ugly, but fast and easy to update.
 *
 * Configuration tips:
 * == X axis ==
 * [0-5-10]__[12-17-22]__[24-25]_[27-28]_[30-31]_[32-33]
 */

namespace InsaLan\TournamentBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use InsaLan\InsaLanBundle\API\Lol;
use InsaLan\TournamentBundle\Entity\Team;

class Placement
{
    public function getStructure()
    {
        $structure = array();
        //                      game    id  x  y  w  h
        $structure[] = array("lol2017", 1, 0, 0, 5, 1);
        $structure[] = array("lol2017", 3, 0, 1, 5, 1);
        $structure[] = array("lol2017", 5, 0, 3, 5, 1);
        $structure[] = array("lol2017", 7, 0, 4, 5, 1);
        $structure[] = array("lol2017", 9, 0, 6, 5, 1);
        $structure[] = array("lol2017", 11, 0, 7, 5, 1);

        $structure[] = array("lol2017", 2,  5, 0, 5, 1);
        $structure[] = array("lol2017", 4,  5, 1, 5, 1);
        $structure[] = array("lol2017", 6,  5, 3, 5, 1);
        $structure[] = array("lol2017", 8,  5, 4, 5, 1);
        $structure[] = array("lol2017", 10, 5, 6, 5, 1);
        $structure[] = array("lol2017", 12, 5, 7, 5, 1);

        $structure[] = array("lol2017", 13, 12, 0, 5, 1);
        $structure[] = array("lol2017", 15, 12, 1, 5, 1);
        $structure[] = array("lol2017", 17, 12, 3, 5, 1);
        $structure[] = array("lol2017", 19, 12, 4, 5, 1);

        $structure[] = array("lol2017", 14, 17, 0, 5, 1);
        $structure[] = array("lol2017", 16, 17, 1, 5, 1);
        $structure[] = array("lol2017", 18, 17, 3, 5, 1);
        $structure[] = array("lol2017", 20, 17, 4, 5, 1);

        //                      game    id   x   y  w  h
        $structure[] = array("csgo2017", 1,  0, 9, 5, 1);
        $structure[] = array("csgo2017", 3,  0, 10, 5, 1);
        $structure[] = array("csgo2017", 5,  0, 12, 5, 1);
        $structure[] = array("csgo2017", 7,  0, 13, 5, 1);
        $structure[] = array("csgo2017", 9,  0, 15, 5, 1);
        $structure[] = array("csgo2017", 11,  0, 16, 5, 1);
        $structure[] = array("csgo2017", 13,  0, 19, 5, 1);
        $structure[] = array("csgo2017", 15,  0, 20, 5, 1);
        $structure[] = array("csgo2017", 17, 0, 22, 5, 1);
        $structure[] = array("csgo2017", 19, 0, 23, 5, 1);
        $structure[] = array("csgo2017", 21, 0, 25, 5, 1);
        $structure[] = array("csgo2017", 23, 0, 26, 5, 1);
        $structure[] = array("csgo2017", 25, 0, 28, 5, 1);
        $structure[] = array("csgo2017", 27, 0, 29, 5, 1);
        $structure[] = array("csgo2017", 29, 0, 31, 5, 1);
        $structure[] = array("csgo2017", 31, 0, 32, 5, 1);
        
        $structure[] = array("csgo2017", 2,  5, 9, 5, 1);
        $structure[] = array("csgo2017", 4,  5, 10, 5, 1);
        $structure[] = array("csgo2017", 6,  5, 12, 5, 1);
        $structure[] = array("csgo2017", 8,  5, 13, 5, 1);
        $structure[] = array("csgo2017", 10,  5, 15, 5, 1);
        $structure[] = array("csgo2017", 12,  5, 16, 5, 1);
        $structure[] = array("csgo2017", 14,  5, 19, 5, 1);
        $structure[] = array("csgo2017", 16,  5, 20, 5, 1);
        $structure[] = array("csgo2017", 18,  5, 22, 5, 1);
        $structure[] = array("csgo2017", 20, 5, 23, 5, 1);
        $structure[] = array("csgo2017", 22, 5, 25, 5, 1);
        $structure[] = array("csgo2017", 24, 5, 26, 5, 1);
        $structure[] = array("csgo2017", 26, 5, 28, 5, 1);
        $structure[] = array("csgo2017", 28, 5, 29, 5, 1);
        $structure[] = array("csgo2017", 30, 5, 31, 5, 1);
        $structure[] = array("csgo2017", 32, 5, 32, 5, 1);

        //                      game     id   x  y  w  h
        $structure[] = array("dota2017", 1, 12, 6, 5, 1);
        $structure[] = array("dota2017", 3, 12, 7, 5, 1);
        $structure[] = array("dota2017", 5, 12, 9, 5, 1);
        $structure[] = array("dota2017", 7, 12, 10, 5, 1);
        $structure[] = array("dota2017", 9, 12, 12, 5, 1);
        $structure[] = array("dota2017", 11, 12, 13, 5, 1);

        $structure[] = array("dota2017", 2, 17, 6, 5, 1);
        $structure[] = array("dota2017", 4, 17, 7, 5, 1);
        $structure[] = array("dota2017", 6, 17, 9, 5, 1);
        $structure[] = array("dota2017", 8, 17, 10, 5, 1);
        $structure[] = array("dota2017", 10, 17, 12, 5, 1);
        $structure[] = array("dota2017", 12, 17, 13, 5, 1);

        $structure[] = array("dota2017", 13, 24, 6, 1, 5);
        $structure[] = array("dota2017", 14, 25, 6, 1, 5);
        $structure[] = array("dota2017", 15, 24, 11, 1, 5);
        $structure[] = array("dota2017", 16, 25, 11, 1, 5);
        $structure[] = array("dota2017", 17, 27, 6, 1, 5);
        $structure[] = array("dota2017", 18, 28, 6, 1, 5);
        $structure[] = array("dota2017", 19, 27, 11, 1, 5);
        $structure[] = array("dota2017", 20, 28, 11, 1, 5);

        $structure[] = array("ow2017", 1, 24, 0, 1, 6);
        $structure[] = array("ow2017", 2, 25, 0, 1, 6);
        $structure[] = array("ow2017", 3, 27, 0, 1, 6);
        $structure[] = array("ow2017", 4, 28, 0, 1, 6);
        $structure[] = array("ow2017", 5, 30, 0, 1, 6);
        $structure[] = array("ow2017", 6, 31, 0, 1, 6);
        $structure[] = array("ow2017", 7, 30, 6, 1, 6);
        $structure[] = array("ow2017", 8, 31, 6, 1, 6);

        $structure[] = array("streamer", 0, 30, 12, 2, 2);
        $structure[] = array("streamer", 1, 30, 14, 2, 2);

        // Initalize counters for HS
        $hs = 1;

        // rightmost table
        for ($j = 0; $j < 16; $j++) {
            $structure[] = array("hs2017", $hs++, 33, $j, 1, 1);
            $structure[] = array("hs2017", $hs++, 34, $j, 1, 1);
        }

        return $structure;
    }
}
