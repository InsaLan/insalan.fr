<?php

/**
 * The service for player placement structure handling.
 * It's very ugly, but fast and easy to update.
 */

namespace InsaLan\TournamentBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use InsaLan\InsaLanBundle\API\Lol;
use InsaLan\TournamentBundle\Entity\Team;

class Placement
{
    public function getStructure() {
        $structure = array();
        //                   game   id  x  y  w  h
        $structure[] = array("lol", 20, 0, 0, 5, 1);
        $structure[] = array("lol", 19, 0, 1, 5, 1);
        $structure[] = array("lol", 16, 0, 3, 5, 1);
        $structure[] = array("lol", 15, 0, 4, 5, 1);
        $structure[] = array("lol", 12, 0, 6, 5, 1);
        $structure[] = array("lol", 11, 0, 7, 5, 1);
        $structure[] = array("lol", 8,  0, 9, 5, 1);
        $structure[] = array("lol", 7,  0, 10, 5, 1);
        $structure[] = array("lol", 4,  0, 12, 5, 1);
        $structure[] = array("lol", 3,  0, 13, 5, 1);
        $structure[] = array("lol", 18, 5, 0, 5, 1);
        $structure[] = array("lol", 17, 5, 1, 5, 1);
        $structure[] = array("lol", 14, 5, 3, 5, 1);
        $structure[] = array("lol", 13, 5, 4, 5, 1);
        $structure[] = array("lol", 10, 5, 6, 5, 1);
        $structure[] = array("lol", 9,  5, 7, 5, 1);
        $structure[] = array("lol", 6,  5, 9, 5, 1);
        $structure[] = array("lol", 5,  5, 10, 5, 1);
        $structure[] = array("lol", 2,  5, 12, 5, 1);
        $structure[] = array("lol", 1,  5, 13, 5, 1);
        $structure[] = array("lol", 32, 12, 0, 5, 1);
        $structure[] = array("lol", 31, 12, 1, 5, 1);
        $structure[] = array("lol", 30, 12, 3, 5, 1);
        $structure[] = array("lol", 29, 12, 4, 5, 1);
        $structure[] = array("lol", 28, 12, 6, 5, 1);
        $structure[] = array("lol", 27, 12, 7, 5, 1);
        $structure[] = array("lol", 24, 12, 9, 5, 1);
        $structure[] = array("lol", 23, 12, 10, 5, 1);
        $structure[] = array("lol", 26, 17, 6, 5, 1);
        $structure[] = array("lol", 25, 17, 7, 5, 1);
        $structure[] = array("lol", 22, 17, 9, 5, 1);
        $structure[] = array("lol", 21, 17, 10, 5, 1);

        $structure[] = array("csgo", 3,  0, 15, 5, 1);
        $structure[] = array("csgo", 4,  0, 16, 5, 1);
        $structure[] = array("csgo", 7,  0, 19, 5, 1);
        $structure[] = array("csgo", 8,  0, 20, 5, 1);
        $structure[] = array("csgo", 11, 0, 22, 5, 1);
        $structure[] = array("csgo", 12, 0, 23, 5, 1);
        $structure[] = array("csgo", 15, 0, 25, 5, 1);
        $structure[] = array("csgo", 16, 0, 26, 5, 1);
        $structure[] = array("csgo", 19, 0, 28, 5, 1);
        $structure[] = array("csgo", 20, 0, 29, 5, 1);
        $structure[] = array("csgo", 23, 0, 31, 5, 1);
        $structure[] = array("csgo", 24, 0, 32, 5, 1);
        $structure[] = array("csgo", 1,  5, 15, 5, 1);
        $structure[] = array("csgo", 2,  5, 16, 5, 1);
        $structure[] = array("csgo", 5,  5, 19, 5, 1);
        $structure[] = array("csgo", 6,  5, 20, 5, 1);
        $structure[] = array("csgo", 9,  5, 22, 5, 1);
        $structure[] = array("csgo", 10, 5, 23, 5, 1);
        $structure[] = array("csgo", 13, 5, 25, 5, 1);
        $structure[] = array("csgo", 14, 5, 26, 5, 1);
        $structure[] = array("csgo", 17, 5, 28, 5, 1);
        $structure[] = array("csgo", 18, 5, 29, 5, 1);
        $structure[] = array("csgo", 21, 5, 31, 5, 1);
        $structure[] = array("csgo", 22, 5, 32, 5, 1);

        $structure[] = array("dota", 16, 24, 8, 1, 5);
        $structure[] = array("dota", 14, 24, 13, 1, 5);
        $structure[] = array("dota", 15, 25, 8, 1, 5);
        $structure[] = array("dota", 13, 25, 13, 1, 5);
        $structure[] = array("dota", 12, 27, 3, 1, 5);
        $structure[] = array("dota", 10, 27, 8, 1, 5);
        $structure[] = array("dota", 8,  27, 13, 1, 5);
        $structure[] = array("dota", 11, 28, 3, 1, 5);
        $structure[] = array("dota", 9,  28, 8, 1, 5);
        $structure[] = array("dota", 7,  28, 13, 1, 5);
        $structure[] = array("dota", 6,  30, 3, 1, 5);
        $structure[] = array("dota", 4,  30, 8, 1, 5);
        $structure[] = array("dota", 2,  30, 13, 1, 5);
        $structure[] = array("dota", 5,  31, 3, 1, 5);
        $structure[] = array("dota", 3,  31, 8, 1, 5);
        $structure[] = array("dota", 1,  31, 13, 1, 5);

        for($i = 32, $j = 1; $i > 0; $i-=2, $j++) {
            $structure[] = array("hs", $i, 33, $j, 1, 1);
            $structure[] = array("hs", $i-1, 34, $j, 1, 1);
        }

        for($i = 1, $j = 0; $i < 6; $i+=2, $j++) {
            $structure[] = array("st2", $i, 31, $j, 1, 1);
            $structure[] = array("st2", $i+1, 30, $j, 1, 1);
            $structure[] = array("st2", $i+6, 28, $j, 1, 1);
            $structure[] = array("st2", $i+7, 27, $j, 1, 1);
        }

        for($i = 13, $j = 0; $i < 28; $i+=2, $j++) {
            $structure[] = array("st2", $i, 25, $j, 1, 1);
            $structure[] = array("st2", $i+1, 24, $j, 1, 1);
        }

        for($i = 30, $j = 21; $i < 39; $i+=2, $j--) {
            $structure[] = array("st2", $i, $j, 0, 1, 1);
            $structure[] = array("st2", $i-1, $j, 1, 1, 1);
            $structure[] = array("st2", $i+10, $j, 3, 1, 1);
            $structure[] = array("st2", $i+9, $j, 4, 1, 1);
        }

        return $structure;
    }
}