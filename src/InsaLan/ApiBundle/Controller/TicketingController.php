<?php

namespace InsaLan\ApiBundle\Controller;

use InsaLan\ApiBundle\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use InsaLan\TicketingBundle\Entity\ETicket;

class TicketingController extends Controller
{

    const ERR_TICKET_NOT_FOUND = array("no" => 1, "msg" => "Ticket not found");
    const ERR_PARTICIPANT_NOT_FOUND = array("no" => 2, "msg" => "Participant not found");
    const ERR_TICKET_ALREADY_SCANNED = array("no" => 3, "msg" => "Already scanned");
    const ERR_TICKET_CANCELLED = array("no" => 4, "msg" => "Ticket cancelled");

    /**
     * @Route("/ticket/login")
     * @Method({"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function loginAction(Request $request)
    {
      return new Response("OK", Response::HTTP_OK);
    }


    /**
     * @Route("/ticket/logout")
     * @Method({"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function logoutAction(Request $request)
    {
      $this->get('security.token_storage')->setToken(null); 
      $this->get('session')->invalidate();
      return new Response("OK", Response::HTTP_OK);
    }

    /**
     * @Route("/ticket/get")
     * @Method({"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function getETicketAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Get JSON data
        $parametersAsArray = [];
            if ($content = $request->getContent()) {
                $parametersAsArray = json_decode($content, true);
            }

        // Find e-ticket
        $token = $parametersAsArray["token"];
        $eTicket = $em->getRepository('InsaLanTicketingBundle:ETicket')->findOneByToken($token);
        if ($eTicket === null) {
          return new JsonResponse(array("err" => self::ERR_TICKET_NOT_FOUND));
        }

        // Find participant
        $participant = $em->getRepository('InsaLanTournamentBundle:Player')->findOneByETicket($eTicket);
        if ($participant === null) {
          $participant = $em->getRepository('InsaLanTournamentBundle:Manager')->findOneByETicket($eTicket);
        }
        if ($participant === null && !$eTicket->isCancelled()) {
          return new JsonResponse(array("err" => self::ERR_PARTICIPANT_NOT_FOUND));
        }

        $res = array(
          "name" => $eTicket->getUser()->getFirstname()." ".$eTicket->getUser()->getLastname(),
          "phone" => $eTicket->getUser()->getPhoneNumber(),
          "tournament" => $eTicket->getTournament()->getName(),
          "ticketScanned" => $eTicket->isScanned(),
          "ticketCancelled" => $eTicket->isCancelled()
          );
        
        if ($participant) {
          $res["gameName"] = $participant->getGameName();
        }
        return new JsonResponse($res);
    }

    /**
     * @Route("/ticket/validate")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method({"POST"})
     */
    public function validateTicketAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();

      // Get JSON data
      $parametersAsArray = [];
          if ($content = $request->getContent()) {
              $parametersAsArray = json_decode($content, true);
          }

      // Find e-ticket
      $token = $parametersAsArray["token"];
      $eTicket = $em->getRepository('InsaLanTicketingBundle:ETicket')->findOneByToken($token);
      if ($eTicket === null) {
        return new JsonResponse(array("err" => self::ERR_TICKET_NOT_FOUND));
      }
      if ($eTicket->getStatus() == ETicket::STATUS_SCANNED) {
        return new JsonResponse(array("err" => self::ERR_TICKET_ALREADY_SCANNED));
      } else if ($eTicket->getStatus() == ETicket::STATUS_VALID){
        $eTicket->setStatus(ETicket::STATUS_SCANNED);
        $em->persist($eTicket);
        $em->flush();
        return new JsonResponse(array("err" => null));
      } else {
        return new JsonResponse(array("err" => self::ERR_TICKET_CANCELLED));
      }
    }
}
