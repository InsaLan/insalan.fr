<?php

/**
 * The service for player placement structure handling.
 * It's very ugly, but fast and easy to update.
 *
 * Configuration tips:
 * == X axis ==
 * [0-5-10]__[12-13]__[15-16]_[18-19]_[22-23-28-33]
 */

namespace InsaLan\TournamentBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use InsaLan\InsaLanBundle\API\Lol;
use InsaLan\TournamentBundle\Entity\Team;

class Placement
{
    public function getStructure() {
        $structure = array();
        //                      game    id  x  y  w  h
        $structure[] = array("lol2019", 1,  0, 0,  5, 1);
        $structure[] = array("lol2019", 3,  0, 1,  5, 1);
        $structure[] = array("lol2019", 5,  0, 3,  5, 1);
        $structure[] = array("lol2019", 7,  0, 4,  5, 1);
        $structure[] = array("lol2019", 9,  0, 6,  5, 1);
        $structure[] = array("lol2019", 11, 0, 7,  5, 1);
        $structure[] = array("lol2019", 13, 0, 9,  5, 1);
        $structure[] = array("lol2019", 15, 0, 10, 5, 1);
        $structure[] = array("lol2019", 17, 0, 12, 5, 1);
        $structure[] = array("lol2019", 19, 0, 13, 5, 1);
        $structure[] = array("lol2019", 21, 0, 15, 5, 1);
        $structure[] = array("lol2019", 23, 0, 16, 5, 1);

        $structure[] = array("lol2019", 2,  5, 0,  5, 1);
        $structure[] = array("lol2019", 4,  5, 1,  5, 1);
        $structure[] = array("lol2019", 6,  5, 3,  5, 1);
        $structure[] = array("lol2019", 8,  5, 4,  5, 1);
        $structure[] = array("lol2019", 10, 5, 6,  5, 1);
        $structure[] = array("lol2019", 12, 5, 7,  5, 1);
        $structure[] = array("lol2019", 14, 5, 9,  5, 1);
        $structure[] = array("lol2019", 16, 5, 10, 5, 1);
        $structure[] = array("lol2019", 18, 5, 12, 5, 1);
        $structure[] = array("lol2019", 20, 5, 13, 5, 1);
        $structure[] = array("lol2019", 22, 5, 15, 5, 1);
        $structure[] = array("lol2019", 24, 5, 16, 5, 1);

        //                      game     id  x   y   w  h
        $structure[] = array("csgo2019", 1,  23, 0,  5, 1);
        $structure[] = array("csgo2019", 4,  23, 1,  5, 1);
        $structure[] = array("csgo2019", 7,  23, 3,  5, 1);
        $structure[] = array("csgo2019", 10, 23, 4,  5, 1);
        $structure[] = array("csgo2019", 13, 23, 6,  5, 1);
        $structure[] = array("csgo2019", 16, 23, 7,  5, 1);
        $structure[] = array("csgo2019", 19, 23, 9,  5, 1);
        $structure[] = array("csgo2019", 22, 23, 10, 5, 1);

        $structure[] = array("csgo2019", 2,  28, 0,  5, 1);
        $structure[] = array("csgo2019", 5,  28, 1,  5, 1);
        $structure[] = array("csgo2019", 8,  28, 3,  5, 1);
        $structure[] = array("csgo2019", 11, 28, 4,  5, 1);
        $structure[] = array("csgo2019", 14, 28, 6,  5, 1);
        $structure[] = array("csgo2019", 17, 28, 7,  5, 1);
        $structure[] = array("csgo2019", 20, 28, 9,  5, 1);
        $structure[] = array("csgo2019", 23, 28, 10, 5, 1);

        $structure[] = array("csgo2019", 3,  33, 0,  5, 1);
        $structure[] = array("csgo2019", 6,  33, 1,  5, 1);
        $structure[] = array("csgo2019", 9,  33, 3,  5, 1);
        $structure[] = array("csgo2019", 12, 33, 4,  5, 1);
        $structure[] = array("csgo2019", 15, 33, 6,  5, 1);
        $structure[] = array("csgo2019", 18, 33, 7,  5, 1);
        $structure[] = array("csgo2019", 21, 33, 9,  5, 1);
        $structure[] = array("csgo2019", 24, 33, 10, 5, 1);

        //                      game    id  x   y   w  h
        $structure[] = array("fbr2019", 1,  12, 0,  1, 4);
        $structure[] = array("fbr2019", 5,  13, 0,  1, 4);
        $structure[] = array("fbr2019", 9,  15, 0,  1, 4);
        $structure[] = array("fbr2019", 13, 16, 0,  1, 4);
        $structure[] = array("fbr2019", 17, 18, 0,  1, 4);
        $structure[] = array("fbr2019", 21, 19, 0,  1, 4);

        $structure[] = array("fbr2019", 2,  12, 4,  1, 4);
        $structure[] = array("fbr2019", 6,  13, 4,  1, 4);
        $structure[] = array("fbr2019", 10, 15, 4,  1, 4);
        $structure[] = array("fbr2019", 14, 16, 4,  1, 4);
        $structure[] = array("fbr2019", 18, 18, 4,  1, 4);
        $structure[] = array("fbr2019", 22, 19, 4,  1, 4); 
        
        $structure[] = array("fbr2019", 3,  12, 8,  1, 4);
        $structure[] = array("fbr2019", 7,  13, 8,  1, 4);
        $structure[] = array("fbr2019", 11, 15, 8,  1, 4);
        $structure[] = array("fbr2019", 15, 16, 8,  1, 4);
        $structure[] = array("fbr2019", 19, 18, 8,  1, 4);
        $structure[] = array("fbr2019", 23, 19, 8,  1, 4); 
        
        $structure[] = array("fbr2019", 4,  12, 12,  1, 4);
        $structure[] = array("fbr2019", 8,  13, 12,  1, 4);
        $structure[] = array("fbr2019", 12, 15, 12,  1, 4);
        $structure[] = array("fbr2019", 16, 16, 12,  1, 4);
        $structure[] = array("fbr2019", 20, 18, 12,  1, 4);
        $structure[] = array("fbr2019", 24, 19, 12,  1, 4);

        
        $structure[] = array("streamer", 0, 22, 0,  1, 2);
        $structure[] = array("streamer", 1, 22, 3,  1, 2);
        $structure[] = array("streamer", 2, 22, 6,  1, 2);
        $structure[] = array("streamer", 3, 22, 9,  1, 2);

        // Initalize counters for HS
        $hs = 1;

        // rightmost table
        for($j = 26; $j < 38; $j++) {
            $structure[] = array("hs2019", $hs++, $j, 12, 1, 1);
            $structure[] = array("hs2019", $hs++, $j, 13, 1, 1);
            $structure[] = array("hs2019", $hs++, $j, 15, 1, 1);
            $structure[] = array("hs2019", $hs++, $j, 16, 1, 1);
        }

        return $structure;
    }
}