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
    public function getStructure() {
        $structure = array();
        //                      game    id  x  y  w  h
        $structure[] = array("lol2018", 1,  0, 0,  5, 1);
        $structure[] = array("lol2018", 3,  0, 1,  5, 1);
        $structure[] = array("lol2018", 5,  0, 3,  5, 1);
        $structure[] = array("lol2018", 7,  0, 4,  5, 1);
        $structure[] = array("lol2018", 9,  0, 6,  5, 1);
        $structure[] = array("lol2018", 11, 0, 7,  5, 1);
        $structure[] = array("lol2018", 13, 0, 9,  5, 1);
        $structure[] = array("lol2018", 15, 0, 10, 5, 1);
        $structure[] = array("lol2018", 17, 0, 12, 5, 1);
        $structure[] = array("lol2018", 19, 0, 13, 5, 1);

        $structure[] = array("lol2018", 2,  5, 0,  5, 1);
        $structure[] = array("lol2018", 4,  5, 1,  5, 1);
        $structure[] = array("lol2018", 6,  5, 3,  5, 1);
        $structure[] = array("lol2018", 8,  5, 4,  5, 1);
        $structure[] = array("lol2018", 10, 5, 6,  5, 1);
        $structure[] = array("lol2018", 12, 5, 7,  5, 1);
        $structure[] = array("lol2018", 14, 5, 9,  5, 1);
        $structure[] = array("lol2018", 16, 5, 10, 5, 1);
        $structure[] = array("lol2018", 18, 5, 12, 5, 1);
        $structure[] = array("lol2018", 20, 5, 13, 5, 1);

        $structure[] = array("lol2018", 21, 12, 0, 5, 1);
        $structure[] = array("lol2018", 23, 12, 1, 5, 1);

        $structure[] = array("lol2018", 22, 17, 0, 5, 1);
        $structure[] = array("lol2018", 24, 17, 1, 5, 1);

        //                      game    id   x   y  w  h
        $structure[] = array("csgo2018", 1,  0, 15, 5, 1);
        $structure[] = array("csgo2018", 3,  0, 16, 5, 1);
        $structure[] = array("csgo2018", 5,  0, 19, 5, 1);
        $structure[] = array("csgo2018", 7,  0, 20, 5, 1);
        $structure[] = array("csgo2018", 9,  0, 22, 5, 1);
        $structure[] = array("csgo2018", 11, 0, 23, 5, 1);
        $structure[] = array("csgo2018", 13, 0, 25, 5, 1);
        $structure[] = array("csgo2018", 15, 0, 26, 5, 1);
        $structure[] = array("csgo2018", 17, 0, 28, 5, 1);
        $structure[] = array("csgo2018", 19, 0, 29, 5, 1);
        $structure[] = array("csgo2018", 21, 0, 31, 5, 1);
        $structure[] = array("csgo2018", 23, 0, 32, 5, 1);

        $structure[] = array("csgo2018", 2,  5, 15, 5, 1);
        $structure[] = array("csgo2018", 4,  5, 16, 5, 1);
        $structure[] = array("csgo2018", 6,  5, 19, 5, 1);
        $structure[] = array("csgo2018", 8,  5, 20, 5, 1);
        $structure[] = array("csgo2018", 10, 5, 22, 5, 1);
        $structure[] = array("csgo2018", 12, 5, 23, 5, 1);
        $structure[] = array("csgo2018", 14, 5, 25, 5, 1);
        $structure[] = array("csgo2018", 16, 5, 26, 5, 1);
        $structure[] = array("csgo2018", 18, 5, 28, 5, 1);
        $structure[] = array("csgo2018", 20, 5, 29, 5, 1);
        $structure[] = array("csgo2018", 22, 5, 31, 5, 1);
        $structure[] = array("csgo2018", 24, 5, 32, 5, 1);

        //                      game     id   x  y  w  h
        $structure[] = array("dota2018", 1,  12, 3,  5, 1);
        $structure[] = array("dota2018", 3,  12, 4,  5, 1);
        $structure[] = array("dota2018", 5,  12, 6,  5, 1);
        $structure[] = array("dota2018", 7,  12, 7,  5, 1);
        $structure[] = array("dota2018", 9,  12, 9,  5, 1);
        $structure[] = array("dota2018", 11, 12, 10, 5, 1);
        $structure[] = array("dota2018", 13, 12, 12, 5, 1);
        $structure[] = array("dota2018", 15, 12, 13, 5, 1);

        $structure[] = array("dota2018", 2,  17, 3,  5, 1);
        $structure[] = array("dota2018", 4,  17, 4,  5, 1);
        $structure[] = array("dota2018", 6,  17, 6,  5, 1);
        $structure[] = array("dota2018", 8,  17, 7,  5, 1);
        $structure[] = array("dota2018", 10, 17, 9,  5, 1);
        $structure[] = array("dota2018", 12, 17, 10, 5, 1);
        $structure[] = array("dota2018", 14, 17, 12, 5, 1);
        $structure[] = array("dota2018", 16, 17, 13, 5, 1);

        $structure[] = array("dota2018", 17, 24, 0,  1, 5);
        $structure[] = array("dota2018", 18, 25, 0,  1, 5);
        $structure[] = array("dota2018", 19, 24, 5,  1, 5);
        $structure[] = array("dota2018", 20, 25, 5,  1, 5);
        $structure[] = array("dota2018", 21, 24, 10, 1, 5);
        $structure[] = array("dota2018", 22, 25, 10, 1, 5);
        $structure[] = array("dota2018", 23, 27, 0,  1, 5);
        $structure[] = array("dota2018", 24, 28, 0,  1, 5);
        $structure[] = array("dota2018", 25, 27, 5,  1, 5);
        $structure[] = array("dota2018", 26, 28, 5,  1, 5);
        $structure[] = array("dota2018", 27, 27, 10, 1, 5);
        $structure[] = array("dota2018", 28, 28, 10, 1, 5);


        $structure[] = array("streamer", 0, 33, 8,  2, 4);
        $structure[] = array("streamer", 1, 33, 12, 2, 4);

        // Initalize counters for HS
        $hs = 1;

        // rightmost table
        for($j = 0; $j < 16; $j++) {
            $structure[] = array("hs2018", $hs++, 30, $j, 1, 1);
            $structure[] = array("hs2018", $hs++, 31, $j, 1, 1);
        }
        for($j = 0; $j < 8; $j++) {
            $structure[] = array("hs2018", $hs++, 33, $j, 1, 1);
            $structure[] = array("hs2018", $hs++, 34, $j, 1, 1);
        }

        return $structure;
    }
}