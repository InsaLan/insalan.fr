<?php

namespace InsaLan\UserBundle\Service;

use InsaLan\UserBundle\Entity\User;

class LoginPlatform
{
    const PLATFORM_OTHER = 'other';
    const PLATFORM_BATTLENET = 'battlenet';
    const PLATFORM_STEAM = 'Steam';

    private $steam_api_key;

    public function __construct($steamkey) {
        $this->steam_api_key = $steamkey;
    }

    /**
     * Get Steam Details
     *
     * @return array
     */
    public function getSteamDetails(User $user) {
        if($user->getSteamId() == null)
            return null;

        $json = file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$this->steam_api_key.'&steamids='.$user->getSteamId());
        if($json != null) {
            $obj = json_decode($json);
            return $obj->response->players[0];
        }
        return null;
    }

    /**
     * Get GameName based on infos provided by the LoginPlatform
     *
     * @return string
     */
    public function getGameName(User $user, $platform) {
        if ($platform == $this::PLATFORM_STEAM)
            return $this->getSteamDetails($user)->personaname;

        if ($platform == $this::PLATFORM_BATTLENET)
            return explode('#',$user->getBattleTag())[0];

        return null;
    }

}