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
        //                      game     id   x   y  w  h
        $structure[] = array("CSGO2022",  1,  1,  1, 5, 1);
        $structure[] = array("CSGO2022",  2,  6,  1, 5, 1);
        $structure[] = array("CSGO2022",  3,  1,  2, 5, 1);
        $structure[] = array("CSGO2022",  4,  6,  2, 5, 1);

        $structure[] = array("CSGO2022",  5,  1,  4, 5, 1);
        $structure[] = array("CSGO2022",  6,  6,  4, 5, 1);
        $structure[] = array("CSGO2022",  7,  1,  5, 5, 1);
        $structure[] = array("CSGO2022",  8,  6,  5, 5, 1);

        $structure[] = array("CSGO2022",  9,  1,  7, 5, 1);
        $structure[] = array("CSGO2022", 10,  6,  7, 5, 1);
        $structure[] = array("CSGO2022", 11,  1,  8, 5, 1);
        $structure[] = array("CSGO2022", 12,  6,  8, 5, 1);

        $structure[] = array("CSGO2022", 13,  1, 11, 5, 1);
        $structure[] = array("CSGO2022", 14,  6, 11, 5, 1);
        $structure[] = array("CSGO2022", 15,  1, 12, 5, 1);
        $structure[] = array("CSGO2022", 16,  6, 12, 5, 1);

        $structure[] = array("CSGO2022", 17,  1, 14, 5, 1);
        $structure[] = array("CSGO2022", 18,  6, 14, 5, 1);
        $structure[] = array("CSGO2022", 19,  1, 15, 5, 1);
        $structure[] = array("CSGO2022", 20,  6, 15, 5, 1);

        $structure[] = array("CSGO2022", 21,  1, 17, 5, 1);
        $structure[] = array("CSGO2022", 22,  6, 17, 5, 1);
        $structure[] = array("CSGO2022", 23,  1, 18, 5, 1);
        $structure[] = array("CSGO2022", 24,  6, 18, 5, 1);

        //                      game    id   x   y  w  h
        $structure[] = array("lol2022",  1, 21,  1, 5, 1);
        $structure[] = array("lol2022",  2, 26,  1, 5, 1);
        $structure[] = array("lol2022",  3, 21,  2, 5, 1);
        $structure[] = array("lol2022",  4, 26,  2, 5, 1);

        $structure[] = array("lol2022",  5, 21,  4, 5, 1);
        $structure[] = array("lol2022",  6, 26,  4, 5, 1);
        $structure[] = array("lol2022",  7, 21,  5, 5, 1);
        $structure[] = array("lol2022",  8, 26,  5, 5, 1);

        $structure[] = array("lol2022",  9, 21,  7, 5, 1);
        $structure[] = array("lol2022", 10, 26,  7, 5, 1);
        $structure[] = array("lol2022", 11, 21,  8, 5, 1);
        $structure[] = array("lol2022", 12, 26,  8, 5, 1);

        $structure[] = array("lol2022", 13, 21, 11, 5, 1);
        $structure[] = array("lol2022", 14, 26, 11, 5, 1);
        $structure[] = array("lol2022", 15, 21, 12, 5, 1);
        $structure[] = array("lol2022", 16, 26, 12, 5, 1);

        $structure[] = array("lol2022", 17, 21, 14, 5, 1);
        $structure[] = array("lol2022", 18, 26, 14, 5, 1);
        $structure[] = array("lol2022", 19, 21, 15, 5, 1);
        $structure[] = array("lol2022", 20, 26, 15, 5, 1);

        $structure[] = array("lol2022", 21, 21, 17, 5, 1);
        $structure[] = array("lol2022", 22, 26, 17, 5, 1);
        $structure[] = array("lol2022", 23, 21, 18, 5, 1);
        $structure[] = array("lol2022", 24, 26, 18, 5, 1);

        // Initalize counters for TrackMania
        $tft = 1;

     
        for($j = 1; $j <= 18; $j++) {
            if ($j!=9 && $j!=10) {
                $structure[] = array("TM2022", $tft++, 15, $j, 1, 1);
                $structure[] = array("TM2022", $tft++, 16, $j, 1, 1);
            }
        }

        return $structure;
    }
}

