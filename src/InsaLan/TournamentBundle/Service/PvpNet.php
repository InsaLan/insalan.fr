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
            $games = $this->API->game()->recent($summoner->getGameId());
            foreach($games as $game)
            {
                if($game->invalid) continue;
                if(intval($game->createDate / 1000) < $dateLimit) continue;

                $teamACode = intval($game->stats->team);
                $teamBCode = ($teamACode === 100 ? 200 : 100);
                $winner    = $game->stats->win;

                $teamAChecked = 1;
                $teamBChecked = 0;

                $players = array();
                $players[$game->teamId.'-'.$game->championId] = $summoner->getGameId();

                foreach ($game->fellowPlayers as $player) {
                    $players[$player->teamId.'-'.$player->championId] = $player->summonerId;

                    if ($player->teamId === $teamACode && $this->isSummonerInTeam($player->summonerId, $teamA)) {
                        $teamAChecked++;
                    }
                    elseif ($player->teamId === $teamBCode && $this->isSummonerInTeam($player->summonerId, $teamB)) {
                        $teamBChecked++;
                    }
                    else {
                        break;
                    }
                }

                if($teamAChecked === 5 && $teamBChecked === 5) {
                    // We have found the right game, and it's correct :)
                    $data = $this->API->match()->match($game->gameId, false)->raw();

                    // If the game is obfuscated, get summoner data from the match history API
                    if (!isset($data['participantIdentities'][key($data['participantIdentities'])]['player'])) {
                        $names = $this->API->summoner()->name($players);

                        foreach ($data['participants'] as &$part) {
                            $summonerId = $players[$part['teamId'].'-'.$part['championId']];
                            $data['participantIdentities'][$part['participantId']]['player'] = array(
                                'summonerId'   => $summonerId,
                                'summonerName' => $names[$summonerId]);
                        }
                    }

                    return array($winner, json_encode($data));
                }
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
     *                       spectators => "specLOBBYONLY"
     *                       name => "default"
     *                       password => "default")
     *
     * @return string
     */
    public function generateUrl($conf = array())
    {
        // Default values

        $map  = @$conf['map']        ?: "map11";
        $type = @$conf['type']       ?: "pick6";
        $size = @$conf['size']       ?: "team5";
        $spec = @$conf['spectators'] ?: "specALL";
        $name = @$conf['name']       ?: "default";
        $id   = @$conf['extra']      ?: uniqid();
        $pass = @$conf['password']   ?: md5($id);

        // Generation
        // https://github.com/Skymirrh/rito-pls/blob/master/gen.php

        $conf = array('name' => $name, 'extra' => $id, 'password' => $pass, 'report' => '');
        $json_base64 = base64_encode(json_encode($conf, JSON_UNESCAPED_SLASHES));
        $url_format = "pvpnet://lol/customgame/joinorcreate/%s/%s/%s/%s/%s";

        return sprintf($url_format, $map, $type, $size, $spec, $json_base64);
    }

    private function isSummonerInTeam($summonerId, Team $team)
    {
        foreach($team->getPlayers()->toArray() as $player) {
            if($player->getGameId() === $summonerId) return true;
        }

        return false;
    }

}
?>
