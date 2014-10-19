<?php

namespace InsaLan\TournamentBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;

class PvpNet
{ 

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
        $pass = @$conf['password']   ?: "default";

        // Generation
        // https://github.com/Skymirrh/rito-pls/blob/master/gen.php
        
        $conf = array('name' => $name, 'password' => $pass);
        $json_base64 = base64_encode(json_encode($conf, JSON_UNESCAPED_SLASHES));
        $url_format = "pvpnet://lol/customgame/joinorcreate/%s/%s/%s/%s/%s";
        return sprintf($url_format, $map, $type, $size, $spec, $json_base64);

    }

}
?>