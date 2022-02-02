<?php

/**
 * The service for player placement structure handling.
 * It's very ugly, but fast and easy to update.
 *
 * Configuration tips:
 * == X axis ==
 * [0-5-10]__[12-13]__[15-16]_[18-19]_[22-23-28-33]
 */

namespace App\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use App\Entity\TournamentTeam;

class TournamentPlacement
{
    public function getStructure() {
        $structure = array();
        //                      game    id  x  y  w  h
        $structure[] = array("csgo2022", 1,  0, 0,  5, 1);
        $structure[] = array("csgo2022", 5,  0, 1,  5, 1);
        $structure[] = array("csgo2022", 9,  0, 3,  5, 1);
        $structure[] = array("csgo2022", 13, 0, 4,  5, 1);
        $structure[] = array("csgo2022", 17, 0, 6,  5, 1);
        $structure[] = array("csgo2022", 21, 0, 7,  5, 1);

        $structure[] = array("csgo2022", 2,  5, 0,  5, 1);
        $structure[] = array("csgo2022", 6,  5, 1,  5, 1);
        $structure[] = array("csgo2022", 10, 5, 3,  5, 1);
        $structure[] = array("csgo2022", 14, 5, 4,  5, 1);
        $structure[] = array("csgo2022", 18, 5, 6,  5, 1);
        $structure[] = array("csgo2022", 22, 5, 7,  5, 1);

        $structure[] = array("csgo2022", 3,  11, 0, 5, 1);
        $structure[] = array("csgo2022", 7,  11, 1, 5, 1);
        $structure[] = array("csgo2022", 11, 11, 3, 5, 1);
        $structure[] = array("csgo2022", 15, 11, 4, 5, 1);
        $structure[] = array("csgo2022", 19, 11, 6, 5, 1);
        $structure[] = array("csgo2022", 23, 11, 7, 5, 1);

        $structure[] = array("csgo2022", 4,  16, 0, 5, 1);
        $structure[] = array("csgo2022", 8,  16, 1, 5, 1);
        $structure[] = array("csgo2022", 12, 16, 3, 5, 1);
        $structure[] = array("csgo2022", 16, 16, 4, 5, 1);
        $structure[] = array("csgo2022", 20, 16, 6, 5, 1);
        $structure[] = array("csgo2022", 24, 16, 7, 5, 1);

        //                      game     id  x   y   w  h
        $structure[] = array("lol2022", 1,  0, 10,  5, 1);
        $structure[] = array("lol2022", 5,  0, 11,  5, 1);
        $structure[] = array("lol2022", 9,  0, 13,  5, 1);
        $structure[] = array("lol2022", 13, 0, 14,  5, 1);
        $structure[] = array("lol2022", 17, 0, 16,  5, 1);
        $structure[] = array("lol2022", 21, 0, 17,  5, 1);

        $structure[] = array("lol2022", 2,  5, 10,  5, 1);
        $structure[] = array("lol2022", 6,  5, 11,  5, 1);
        $structure[] = array("lol2022", 10, 5, 13,  5, 1);
        $structure[] = array("lol2022", 14, 5, 14,  5, 1);
        $structure[] = array("lol2022", 18, 5, 16,  5, 1);
        $structure[] = array("lol2022", 22, 5, 17,  5, 1);

        $structure[] = array("lol2022", 3,  11, 10, 5, 1);
        $structure[] = array("lol2022", 7,  11, 11, 5, 1);
        $structure[] = array("lol2022", 11, 11, 13, 5, 1);
        $structure[] = array("lol2022", 15, 11, 14, 5, 1);
        $structure[] = array("lol2022", 19, 11, 16, 5, 1);
        $structure[] = array("lol2022", 23, 11, 17, 5, 1);

        $structure[] = array("lol2022", 4,  16, 10, 5, 1);
        $structure[] = array("lol2022", 8,  16, 11, 5, 1);
        $structure[] = array("lol2022", 12, 16, 13, 5, 1);
        $structure[] = array("lol2022", 16, 16, 14, 5, 1);
        $structure[] = array("lol2022", 20, 16, 16, 5, 1);
        $structure[] = array("lol2022", 24, 16, 17, 5, 1);

        //                      game    id  x   y   w  h
        $structure[] = array("dota2022", 1,  27, 0,  5, 1);
        $structure[] = array("dota2022", 3,  27, 1,  5, 1);
        $structure[] = array("dota2022", 5,  27, 3,  5, 1);
        $structure[] = array("dota2022", 7,  27, 4,  5, 1);
        $structure[] = array("dota2022", 9,  27, 6,  5, 1);
        $structure[] = array("dota2022", 11, 27, 7,  5, 1);

        $structure[] = array("dota2022", 13, 27, 10,  5, 1);
        $structure[] = array("dota2022", 15, 27, 11,  5, 1);
        $structure[] = array("dota2022", 17, 27, 13,  5, 1);
        $structure[] = array("dota2022", 19, 27, 14,  5, 1);
        $structure[] = array("dota2022", 21, 27, 16,  5, 1);
        $structure[] = array("dota2022", 23, 27, 17,  5, 1);

        $structure[] = array("dota2022", 2,  32, 0,  5, 1);
        $structure[] = array("dota2022", 4,  32, 1,  5, 1);
        $structure[] = array("dota2022", 6,  32, 3,  5, 1);
        $structure[] = array("dota2022", 8,  32, 4,  5, 1);
        $structure[] = array("dota2022", 10, 32, 6,  5, 1);
        $structure[] = array("dota2022", 12, 32, 7,  5, 1);

        $structure[] = array("dota2022", 14, 32, 10, 5, 1);
        $structure[] = array("dota2022", 16, 32, 11, 5, 1);
        $structure[] = array("dota2022", 18, 32, 13, 5, 1);
        $structure[] = array("dota2022", 20, 32, 14, 5, 1);
        $structure[] = array("dota2022", 22, 32, 16, 5, 1);
        $structure[] = array("dota2022", 24, 32, 17, 5, 1);

        // Initalize counters for TFT
        $tft = 1;

        // rightmost table 23
        for($j = 0; $j < 18; $j++) {
            if ($j!=8 && $j!=9) {
                $structure[] = array("TM2022", $tft++, 23, $j, 1, 1);
                $structure[] = array("TM2022", $tft++, 24, $j, 1, 1);
            }
        }

        return $structure;
    }
}