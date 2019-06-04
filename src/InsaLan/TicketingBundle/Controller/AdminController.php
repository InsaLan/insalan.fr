<?php

namespace InsaLan\TicketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use InsaLan\TournamentBundle\Entity\Player;
use InsaLan\TournamentBundle\Entity\Manager;
use InsaLan\TournamentBundle\Entity\Tournament;
use InsaLan\TournamentBundle\Entity\Participant;
use InsaLan\TicketingBundle\Entity\ETicket;

class AdminController extends Controller
{
    /**
     * @Route("/admin")
     * @Template()
     */
    public function indexAction()
    {
      $em = $this->getDoctrine()->getManager();
      $tournaments = $em->getRepository('InsaLanTournamentBundle:Tournament')->getUpcomingTournaments();
      $soloTournaments = $teamTournaments = array();
      foreach ($tournaments as $tournament) {
        if ($tournament->getParticipantType() == 'team') {
          $teamTournaments[] = $tournament;
        } else if ($tournament->getParticipantType() == 'player'){
          $soloTournaments[] = $tournament;
        }
      }
      $soloPlayers = $em->getRepository('InsaLanTournamentBundle:Player')->findBy(["tournament" => $soloTournaments, "validated" => Participant::STATUS_VALIDATED]);
      $teamPlayers = $em->getRepository('InsaLanTournamentBundle:Player')->getValidatedTeamPlayers($teamTournaments);
      $players = $soloPlayers + $teamPlayers;

      // get validated managers
      $managers = array();
      $teams = $em->getRepository('InsaLanTournamentBundle:Team')->findBy(["tournament" => $teamTournaments, "validated" => Participant::STATUS_VALIDATED]);
      foreach ($soloPlayers as $player) {
        if ($player->getManager() && $player->getManager()->isOk()) {
          $managers[] = $player->getManager();
        }
      }
      foreach ($teams as $team) {
        if ($team->getManager() && $team->getManager()->isOk()) {
          $managers[] = $team->getManager();
        }
      }
      return array("players" => $players, "managers" => $managers);
    }

    /**
     * @Route("/admin/ticket/send")
     * @Method({"POST"})
     */
    public function sendETicketAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        // Find the player or manager
        if ($request->request->get('player')) {
          $participant = $em->getRepository('InsaLanTournamentBundle:Player')->findOneById($request->request->get('player'));
          $isManager = false;
        } elseif ($request->request->get('manager')) {
          $participant = $em->getRepository('InsaLanTournamentBundle:Manager')->findOneById($request->request->get('manager'));
          $isManager = true;
        } else {
          $this->get('session')->getFlashBag()->add('error', "Pas de joueur ni de manager correspondant.");
          return $this->redirect($this->generateUrl("insalan_ticketing_admin_index"));
        }

        // Check csrf token
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('send' . $participant->getId(), $submittedToken)) {
          $this->get('session')->getFlashBag()->add('error', "Token csrf invalide.");
          return $this->redirect($this->generateUrl("insalan_ticketing_admin_index"));
        }

        // Check if a ticket already exists
        if ($participant->getETicket()) {
          $this->get('session')->getFlashBag()->add('error', "Billet déjà créé ");
          return $this->redirect($this->generateUrl("insalan_ticketing_admin_index"));
        }

        // tournament is not in the same field between player and manager
        if ($isManager) {
          $tournament = $participant->getTournament();
        } else {
          $tournament = $participant->getPendingRegistrable();
        }

        $eTicket = new ETicket();
        $eTicket->setUser($participant->getUser())
                ->setTournament($tournament)
                ->setToken($participant->getId() . $tournament); // TODO to be improved
        $participant->setETicket($eTicket);
        $em->persist($eTicket);
        $em->persist($participant);
        $em->flush();

        // Generate pdf
        $this->generateETicket($eTicket);
        $this->get('session')->getFlashBag()->add('info', "Billet créé ".$eTicket->getToken());

        $this->sendETicket($eTicket);
        return $this->redirect($this->generateUrl("insalan_ticketing_admin_index"));
    }

    /**
     * @Route("/admin/ticket/remove")
     * @Method({"POST"})
     */
    public function removeETicketAction(Request $request) {
      $em = $this->getDoctrine()->getManager();

      // Find the player or manager
      if ($request->request->get('player')) {
        $participant = $em->getRepository('InsaLanTournamentBundle:Player')->findOneById($request->request->get('player'));
        $isManager = false;
      } elseif ($request->request->get('manager')) {
        $participant = $em->getRepository('InsaLanTournamentBundle:Manager')->findOneById($request->request->get('manager'));
        $isManager = true;
      } else {
        $this->get('session')->getFlashBag()->add('error', "Pas de joueur ni de manager correspondant.");
        return $this->redirect($this->generateUrl("insalan_ticketing_admin_index"));
      }

      // Check csrf token
      $submittedToken = $request->request->get('_token');
      if (!$this->isCsrfTokenValid('remove' . $participant->getId(), $submittedToken)) {
        $this->get('session')->getFlashBag()->add('error', "Token csrf invalide.");
        return $this->redirect($this->generateUrl("insalan_ticketing_admin_index"));
      }

      $eTicket = $participant->getETicket();
      $participant->setETicket(null);
      $em->persist($participant);
      $em->remove($eTicket);
      $em->flush();

      $this->get('session')->getFlashBag()->add('info', "Billet annulé ");
      return $this->redirect($this->generateUrl("insalan_ticketing_admin_index"));
    }

    private function sendETicket(ETicket $eTicket) {
      $mailer = $this->get('mailer');
      // Create the message
      $message = (new \Swift_Message())
          ->setSubject('Votre inscription au tournoi ' . $eTicket->getTournament())
          ->setFrom(['contact@insalan.fr' => 'InsaLan'])
          ->setTo([$eTicket->getUser()->getEmail()])
          ->setBody(
              $this->renderView(
                  'InsaLanTicketingBundle:Emails:registration.html.twig',
                  ['user' => $eTicket->getUser(),
                   'tournament' => $eTicket->getTournament()
                 ]
              ),
              'text/html'
          );
      $data = $this->generateETicket($eTicket);
      $attachment = (new \Swift_Attachment())
          ->setFilename('Billet_InsaLan.pdf')
          ->setContentType('application/pdf')
          ->setBody($data)
          ;
      $message->attach($attachment);

      $mailer->send($message);

      $em = $this->getDoctrine()->getManager();
      $eTicket->setSentAt(new \DateTime("now"));
      $em->persist($eTicket);
      $em->flush();
      $this->get('session')->getFlashBag()->add('info', "Billet envoyé");
    }

    private function generateETicket(ETicket $eTicket) {
      // TODO:
      //$this->get('session')->getFlashBag()->add('info', "PDF généré");
    }
}
