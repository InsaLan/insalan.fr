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
        $structure[] = array("lol2023",  1,  1,  1, 5, 1);
        $structure[] = array("lol2023",  2,  6,  1, 5, 1);
        $structure[] = array("lol2023",  3,  1,  2, 5, 1);
        $structure[] = array("lol2023",  4,  6,  2, 5, 1);
                                                
        $structure[] = array("lol2023",  5,  1,  4, 5, 1);
        $structure[] = array("lol2023",  6,  6,  4, 5, 1);
        $structure[] = array("lol2023",  7,  1,  5, 5, 1);
        $structure[] = array("lol2023",  8,  6,  5, 5, 1);
                                                
        $structure[] = array("lol2023",  9,  1,  7, 5, 1);
        $structure[] = array("lol2023", 10,  6,  7, 5, 1);
        $structure[] = array("lol2023", 11,  1,  8, 5, 1);
        $structure[] = array("lol2023", 12,  6,  8, 5, 1);
                                                
        $structure[] = array("lol2023", 13,  1, 11, 5, 1);
        $structure[] = array("lol2023", 14,  6, 11, 5, 1);
        $structure[] = array("lol2023", 15,  1, 12, 5, 1);
        $structure[] = array("lol2023", 16,  6, 12, 5, 1);
                                                
        $structure[] = array("lol2023", 17,  1, 14, 5, 1);
        $structure[] = array("lol2023", 18,  6, 14, 5, 1);
        $structure[] = array("lol2023", 19,  1, 15, 5, 1);
        $structure[] = array("lol2023", 20,  6, 15, 5, 1);
                                                
        $structure[] = array("lol2023", 21,  1, 17, 5, 1);
        $structure[] = array("lol2023", 22,  6, 17, 5, 1);
        $structure[] = array("lol2023", 23,  1, 18, 5, 1);
        $structure[] = array("lol2023", 24,  6, 18, 5, 1);

        //                      game    id   x   y  w  h
        $structure[] = array("CSGO2023",  1, 13,  1, 5, 1);
        $structure[] = array("CSGO2023",  2, 18,  1, 5, 1);
        $structure[] = array("CSGO2023",  3, 13,  2, 5, 1);
        $structure[] = array("CSGO2023",  4, 18,  2, 5, 1);
                                                 
        $structure[] = array("CSGO2023",  5, 13,  4, 5, 1);
        $structure[] = array("CSGO2023",  6, 18,  4, 5, 1);
        $structure[] = array("CSGO2023",  7, 13,  5, 5, 1);
        $structure[] = array("CSGO2023",  8, 18,  5, 5, 1);
                                                 
        $structure[] = array("CSGO2023",  9, 13,  7, 5, 1);
        $structure[] = array("CSGO2023", 10, 18,  7, 5, 1);
        $structure[] = array("CSGO2023", 11, 13,  8, 5, 1);
        $structure[] = array("CSGO2023", 12, 18,  8, 5, 1);
                                                 
        $structure[] = array("CSGO2023", 13, 13, 11, 5, 1);
        $structure[] = array("CSGO2023", 14, 18, 11, 5, 1);
        $structure[] = array("CSGO2023", 15, 13, 12, 5, 1);
        $structure[] = array("CSGO2023", 16, 18, 12, 5, 1);
                                                 
        $structure[] = array("CSGO2023", 17, 13, 14, 5, 1);
        $structure[] = array("CSGO2023", 18, 18, 14, 5, 1);
        $structure[] = array("CSGO2023", 19, 13, 15, 5, 1);
        $structure[] = array("CSGO2023", 20, 18, 15, 5, 1);
                                                 
        $structure[] = array("CSGO2023", 13, 13, 17, 5, 1);
        $structure[] = array("CSGO2023", 22, 18, 17, 5, 1);
        $structure[] = array("CSGO2023", 23, 13, 18, 5, 1);
        $structure[] = array("CSGO2023", 24, 18, 18, 5, 1);

        // Initalize counters for TrackMania
        $tm = 1;
     
        for($j = 11; $j <= 18; $j++) {
		$structure[] = array("TM2023", $tm++, 24, $j, 1, 1);
		$structure[] = array("TM2023", $tm++, 25, $j, 1, 1);
		$structure[] = array("TM2023", $tm++, 28, $j, 1, 1);
		$structure[] = array("TM2023", $tm++, 29, $j, 1, 1);
		$structure[] = array("TM2023", $tm++, 31, $j, 1, 1);
		$structure[] = array("TM2023", $tm++, 32, $j, 1, 1);
		$structure[] = array("TM2023", $tm++, 34, $j, 1, 1);
		$structure[] = array("TM2023", $tm++, 35, $j, 1, 1);
        }

        // Initalize counters for TrackMania
        $rl = 1;
     
        for($j = 0; $j < 3; $j++) {
		$structure[] = array("RL2023", $rl++, 24, 1+3*$j, 1, 3);
		$structure[] = array("RL2023", $rl++, 25, 1+3*$j, 1, 3);
		$structure[] = array("RL2023", $rl++, 28+3*$j, 1, 3, 1);
		$structure[] = array("RL2023", $rl++, 28+3*$j, 2, 3, 1);
		$structure[] = array("RL2023", $rl++, 28+3*$j, 4, 3, 1);
		$structure[] = array("RL2023", $rl++, 28+3*$j, 5, 3, 1);
		$structure[] = array("RL2023", $rl++, 28+3*$j, 7, 3, 1);
		$structure[] = array("RL2023", $rl++, 28+3*$j, 8, 3, 1);
	}

        return $structure;
    }
}

