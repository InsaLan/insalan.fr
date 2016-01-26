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
        $structure[] = array("lol2016", 20, 0, 0, 5, 1);
        $structure[] = array("lol2016", 19, 0, 1, 5, 1);
        $structure[] = array("lol2016", 16, 0, 3, 5, 1);
        $structure[] = array("lol2016", 15, 0, 4, 5, 1);
        $structure[] = array("lol2016", 12, 0, 6, 5, 1);
        $structure[] = array("lol2016", 11, 0, 7, 5, 1);
        $structure[] = array("lol2016", 8,  0, 9, 5, 1);
        $structure[] = array("lol2016", 7,  0, 10, 5, 1);
        $structure[] = array("lol2016", 4,  0, 12, 5, 1);
        $structure[] = array("lol2016", 3,  0, 13, 5, 1);
        $structure[] = array("lol2016", 18, 5, 0, 5, 1);
        $structure[] = array("lol2016", 17, 5, 1, 5, 1);
        $structure[] = array("lol2016", 14, 5, 3, 5, 1);
        $structure[] = array("lol2016", 13, 5, 4, 5, 1);
        $structure[] = array("lol2016", 10, 5, 6, 5, 1);
        $structure[] = array("lol2016", 9,  5, 7, 5, 1);
        $structure[] = array("lol2016", 6,  5, 9, 5, 1);
        $structure[] = array("lol2016", 5,  5, 10, 5, 1);
        $structure[] = array("lol2016", 2,  5, 12, 5, 1);
        $structure[] = array("lol2016", 1,  5, 13, 5, 1);
        $structure[] = array("lol2016", 32, 12, 0, 5, 1);
        $structure[] = array("lol2016", 31, 12, 1, 5, 1);
        $structure[] = array("lol2016", 30, 12, 3, 5, 1);
        $structure[] = array("lol2016", 29, 12, 4, 5, 1);
        $structure[] = array("lol2016", 28, 12, 6, 5, 1);
        $structure[] = array("lol2016", 27, 12, 7, 5, 1);
        $structure[] = array("lol2016", 24, 12, 9, 5, 1);
        $structure[] = array("lol2016", 23, 12, 10, 5, 1);
        $structure[] = array("lol2016", 26, 17, 6, 5, 1);
        $structure[] = array("lol2016", 25, 17, 7, 5, 1);
        $structure[] = array("lol2016", 22, 17, 9, 5, 1);
        $structure[] = array("lol2016", 21, 17, 10, 5, 1);
        //                      game    id   x   y  w  h
        $structure[] = array("csgo2016", 3,  0, 15, 5, 1);
        $structure[] = array("csgo2016", 4,  0, 16, 5, 1);
        $structure[] = array("csgo2016", 7,  0, 19, 5, 1);
        $structure[] = array("csgo2016", 8,  0, 20, 5, 1);
        $structure[] = array("csgo2016", 11, 0, 22, 5, 1);
        $structure[] = array("csgo2016", 12, 0, 23, 5, 1);
        $structure[] = array("csgo2016", 15, 0, 25, 5, 1);
        $structure[] = array("csgo2016", 16, 0, 26, 5, 1);
        $structure[] = array("csgo2016", 19, 0, 28, 5, 1);
        $structure[] = array("csgo2016", 20, 0, 29, 5, 1);
        $structure[] = array("csgo2016", 23, 0, 31, 5, 1);
        $structure[] = array("csgo2016", 24, 0, 32, 5, 1);
        $structure[] = array("csgo2016", 1,  5, 15, 5, 1);
        $structure[] = array("csgo2016", 2,  5, 16, 5, 1);
        $structure[] = array("csgo2016", 5,  5, 19, 5, 1);
        $structure[] = array("csgo2016", 6,  5, 20, 5, 1);
        $structure[] = array("csgo2016", 9,  5, 22, 5, 1);
        $structure[] = array("csgo2016", 10, 5, 23, 5, 1);
        $structure[] = array("csgo2016", 13, 5, 25, 5, 1);
        $structure[] = array("csgo2016", 14, 5, 26, 5, 1);
        $structure[] = array("csgo2016", 17, 5, 28, 5, 1);
        $structure[] = array("csgo2016", 18, 5, 29, 5, 1);
        $structure[] = array("csgo2016", 21, 5, 31, 5, 1);
        $structure[] = array("csgo2016", 22, 5, 32, 5, 1);
        //                      game     id   x  y  w  h
        $structure[] = array("dota2016", 16, 24, 8, 1, 5);
        $structure[] = array("dota2016", 14, 24, 13, 1, 5);
        $structure[] = array("dota2016", 15, 25, 8, 1, 5);
        $structure[] = array("dota2016", 13, 25, 13, 1, 5);
        $structure[] = array("dota2016", 12, 27, 3, 1, 5);
        $structure[] = array("dota2016", 10, 27, 8, 1, 5);
        $structure[] = array("dota2016", 8,  27, 13, 1, 5);
        $structure[] = array("dota2016", 11, 28, 3, 1, 5);
        $structure[] = array("dota2016", 9,  28, 8, 1, 5);
        $structure[] = array("dota2016", 7,  28, 13, 1, 5);
        $structure[] = array("dota2016", 6,  30, 3, 1, 5);
        $structure[] = array("dota2016", 4,  30, 8, 1, 5);
        $structure[] = array("dota2016", 2,  30, 13, 1, 5);
        $structure[] = array("dota2016", 5,  31, 3, 1, 5);
        $structure[] = array("dota2016", 3,  31, 8, 1, 5);
        $structure[] = array("dota2016", 1,  31, 13, 1, 5);

        // Initalize counters for HS and SC2
        $hs = 48;
        $sc = 32;

        // rightmost table
        for($j = 0; $j < 16; $j++) {
            $structure[] = array("hs2016", $hs--, 33, $j, 1, 1);
            $structure[] = array("hs2016", $hs--, 34, $j, 1, 1);
        }

        // rightmost middle tables top
        for($i = 1, $j = 0; $i < 6; $i+=2, $j++) {
            // right
            $structure[] = array("st2016", $sc--, 31, $j, 1, 1);
            $structure[] = array("st2016", $sc--, 30, $j, 1, 1);
            // left
            $structure[] = array("hs2016", $hs--, 28, $j, 1, 1);
            $structure[] = array("hs2016", $hs--, 27, $j, 1, 1);
        }

        // rightmost left table top half
        for($i = 13, $j = 0; $i < 28; $i+=2, $j++) {
            $structure[] = array("st2016", $sc--, 25, $j, 1, 1);
            $structure[] = array("st2016", $sc--, 24, $j, 1, 1);
        }

        for($i = 30, $j = 21; $i < 39; $i+=2, $j--) {
            // mid top table right
            $structure[] = array("hs2016", $hs--, $j, 0, 1, 1);
            $structure[] = array("hs2016", $hs--, $j, 1, 1, 1);
            // mid second table right
            $structure[] = array("st2016", $sc--, $j, 3, 1, 1);
            $structure[] = array("st2016", $sc--, $j, 4, 1, 1);
        }

        return $structure;
    }
}