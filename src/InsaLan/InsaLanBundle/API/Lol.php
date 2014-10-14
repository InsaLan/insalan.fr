<?php
namespace InsaLan\InsaLanBundle\API;

use LeagueWrap\Api;

class Lol
{
  protected $api;

  public function __construct($apiKey) {
    $this->api = new Api($apiKey);
    $this->api->setRegion('euw');
    //$this->api->remember(20);
    //$this->api->limit(10, 10);    // Set a limit of 10 requests per 10 seconds
    //$this->api->limit(500, 600);  // Set a limit of 500 requests per 600 (10 minutes) seconds
  }

  public function getApi() {
    return $this->api;
  }
}
