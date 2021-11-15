<?php

namespace Tests\App\Controller;

use App\Http\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

use App\Entity\ETicket;

class TicketingControllerTest extends WebTestCase
{
  private $client = null;

  public function setUp()
  {
      $this->client = static::createClient();
  }

  public function testLoginSuccess()
  {
    $this->client->request('POST', '/api/ticket/login', [], [], [
      'PHP_AUTH_USER' => 'admin',
      'PHP_AUTH_PW'   => 'admin',
      ]);
    $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
  }

  public function testLoginFailBadCredentials()
  {
    $this->client->request('POST', '/api/ticket/login', [], [], [
      'PHP_AUTH_USER' => 'admin',
      'PHP_AUTH_PW'   => 'passwd',
      ]);
    $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
  }

  public function testLoginFailNotAdmin()
  {
    $this->client->request('POST', '/api/ticket/login', [], [], [
      'PHP_AUTH_USER' => 'user',
      'PHP_AUTH_PW'   => 'user',
      ]);
    $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
  }

  public function testLogoutResponse()
  {
    // TODO: Find a way to mock login
    $this->client->request('POST', '/api/ticket/login', [], [], [
      'PHP_AUTH_USER' => 'admin',
      'PHP_AUTH_PW'   => 'admin',
      ]);
    $this->client->request('POST', '/api/ticket/logout');
    $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
  }

  public function testLogoutCookieDestruction()
  {
    // TODO: Find a way to mock login
    $this->client->request('POST', '/api/ticket/login', [], [], [
      'PHP_AUTH_USER' => 'admin',
      'PHP_AUTH_PW'   => 'admin',
      ]);
    $this->client->request('POST', '/api/ticket/logout');
    $this->client->request('POST', '/api/ticket/logout');
    $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
  }

}
