<?php

namespace InsaLan\TournamentBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use InsaLan\InsaLanBundle\API\Lol;
use InsaLan\TournamentBundle\Entity\Team;

class PvpNet
{ 

    private $API;

    public function __construct(Lol $API)
    {
        $this->API = $API->getApi();
    }

    /**
     * Get the game result between teamA and teamB.
     * Throws an exception if no game was found.
     * @param  Team          $teamA Teams can have more players than 5.     
     * @param  Team          $teamB     
     * @param  phpTimestamp  $dateLimit Games before this timestamp will not be analyzed
     *                                  Ideally, that should be the pvpNetUrl generation date
     * @return true if teamA won, false otherwise.
     */
    public function getGameResult(Team $teamA, Team $teamB, $dateLimit = null)
    {   

        if($dateLimit === null)
            $dateLimit = time() - 3600 * 24; //One day

        foreach($teamA->getPlayers()->toArray() as $summoner)
        {   
            $games = $this->API->game()->recent($summoner->getLolId());
            foreach($games as $game) 
            {

                if($game->invalid) continue;
                if(intval($game->createDate / 1000) < $dateLimit) continue;

                $teamACode = intval($game->stats->team);
                $teamBCode = ($teamACode === 100 ? 200 : 100);
                $winner    = $game->stats->win;

                $teamAChecked = 1;
                $teamBChecked = 0;

                foreach($game->fellowPlayers as $player) {
                    if($player->teamId === $teamACode && $this->isSummonerInTeam($player->summonerId, $teamA))
                        $teamAChecked++;
                    elseif($player->teamId === $teamBCode && $this->isSummonerInTeam($player->summonerId, $teamB))
                        $teamBChecked++;
                }

                if($teamAChecked === 5 && $teamBChecked === 5)
                    return $winner; //We have found the right game, and it's correct :)

            }

        }

        // Not found
        throw new \Exception("Aucune partie ne peut être validée. Avez vous bien joué la partie ?");
    }

    /**
     * Generate a PVPNet url to join or create a custom game (LoL)
     * 
     * @param  array  $conf Array of optionnals parameters
     * 
     *                      (map => "map1"
     *                       type => "pick6"
     *                       size => 5
     *                       spectators => "specALL"
     *                       name => "default"
     *                       password => "default")
     *                       
     * @return string
     */
    public function generateUrl($conf = array())
    {
        // Default values
        
        $map  = @$conf['map']        ?: "map1";  
        $type = @$conf['type']       ?: "pick6";
        $size = @$conf['size']       ?: "5";
        $spec = @$conf['spectators'] ?: "specALL"; 
        $name = @$conf['name']       ?: "default";
        $pass = @$conf['password']   ?: "l1ttl3k1tt!";

        // Generation
        // https://github.com/Skymirrh/rito-pls/blob/master/gen.php
        
        $conf = array('name' => $name, 'password' => $pass);
        $json_base64 = base64_encode(json_encode($conf, JSON_UNESCAPED_SLASHES));
        $url_format = "pvpnet://lol/customgame/joinorcreate/%s/%s/%s/%s/%s";
        return sprintf($url_format, $map, $type, $size, $spec, $json_base64);

    }

    private function isSummonerInTeam($summonerId, Team $team)
    {
        foreach($team->getPlayers()->toArray() as $player) {
            if($player->getLolId() === $summonerId) return true;
        }

        return false;
    }

}
?>