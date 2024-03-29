<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
use Endroid\QrCode\QrCode;

use App\Entity\Player;
use App\Entity\TournamentManager;
use App\Entity\Tournament;
use App\Entity\Participant;
use App\Entity\ETicket;

/**
 * @Route("/admin")
 */
class AdminTicketingController extends Controller
{
    /**
     * @Route("/ticketing/ready")
     * @Template()
     */
    public function readyAction()
    {
      $em = $this->getDoctrine()->getManager();
      $tournaments = $em->getRepository('App\Entity\Tournament')->getUpcomingTournaments();
      $soloTournaments = $teamTournaments = array();
      foreach ($tournaments as $tournament) {
        if ($tournament->getParticipantType() == 'team') {
          $teamTournaments[] = $tournament;
        } else if ($tournament->getParticipantType() == 'player'){
          $soloTournaments[] = $tournament;
        }
      }
      $soloPlayers = $em->getRepository('App\Entity\Player')->findBy(["tournament" => $soloTournaments, "validated" => Participant::STATUS_VALIDATED, "eTicket" => null]);
      $teamPlayers = $em->getRepository('App\Entity\Player')->getValidatedTeamPlayers($teamTournaments);
      $players = array_merge($soloPlayers, $teamPlayers);

      // get validated managers
      $managers = array();
      $teams = $em->getRepository('App\Entity\TournamentTeam')->findBy(["tournament" => $teamTournaments, "validated" => Participant::STATUS_VALIDATED]);
      foreach ($soloPlayers as $player) {
        if ($player->getManager() && $player->getManager()->isOk() && $player->getManager()->getETicket() == null) {
          $managers[] = $player->getManager();
        }
      }
      foreach ($teams as $team) {
        if ($team->getManager() && $team->getManager()->isOk() && $team->getManager()->getETicket() == null) {
          $managers[] = $team->getManager();
        }
      }
      return array("players" => $players, "managers" => $managers);
    }

    /**
     * @Route("/ticketing/sent")
     * @Template()
     */
    public function sentAction()
    {
      $em = $this->getDoctrine()->getManager();
      $eTickets = $em->getRepository('App\Entity\ETicket')->findByStatus([ETicket::STATUS_VALID, ETicket::STATUS_SCANNED]);
      return array("eTickets" => $eTickets);
    }

    /**
     * @Route("/ticketing/cancelled")
     * @Template()
     */
    public function cancelledAction()
    {
      $em = $this->getDoctrine()->getManager();
      $eTickets = $em->getRepository('App\Entity\ETicket')->findByStatus(ETicket::STATUS_CANCELLED);
      return array("eTickets" => $eTickets);
    }

    /**
     * @Route("/ticketing/ticket/send")
     * @Method({"POST"})
     */
    public function sendETicketAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        // Find the player or manager
        if ($request->request->get('player')) {
          $participant = $em->getRepository('App\Entity\Player')->findOneById($request->request->get('player'));
          $isManager = false;
        } elseif ($request->request->get('manager')) {
          $participant = $em->getRepository('App\Entity\TournamentManager')->findOneById($request->request->get('manager'));
          $isManager = true;
        } else {
          $this->get('session')->getFlashBag()->add('error', "Pas de joueur ni de manager correspondant.");
          return $this->redirect($this->generateUrl("app_adminticketing_ready"));
        }

        // Check csrf token
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('send' . $participant->getId(), $submittedToken)) {
          $this->get('session')->getFlashBag()->add('error', "Token csrf invalide.");
          return $this->redirect($this->generateUrl("app_adminticketing_ready"));
        }

        // Check if a ticket already exists
        if ($participant->getETicket()) {
          $this->get('session')->getFlashBag()->add('error', "Billet déjà créé ");
          return $this->redirect($this->generateUrl("app_adminticketing_ready"));
        }

        // tournament is not in the same field between player and manager
        if ($isManager) {
          $tournament = $participant->getTournament();
        } else {
          $tournament = $participant->getPendingRegistrable();
        }

        try {
          // The chances of having the same token twice are very small. The try/catch handle this case.
          $token = sha1(uniqid(mt_rand(), true));

          $eTicket = new ETicket();
          $eTicket->setUser($participant->getUser())
                  ->setTournament($tournament)
                  ->setToken($token);
          $participant->setETicket($eTicket);
          $em->persist($eTicket);
          $em->persist($participant);
          $em->flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
          $this->get('session')->getFlashBag()->add('error', "Le token généré est déjà utilisé. Réessayez dans quelques instants.\n Token : " . $token);
          return $this->redirect($this->generateUrl("app_adminticketing_ready"));
        }

        // Generate pdf
        $pdf = $this->generateETicket($eTicket, $participant);
        $this->get('session')->getFlashBag()->add('info', "Billet créé ".$eTicket->getToken());

        $this->sendETicket($eTicket, $pdf);
        return $this->redirect($this->generateUrl("app_adminticketing_ready"));
    }

    /**
     * @Route("/ticketing/ticket/remove")
     * @Method({"POST"})
     */
    public function removeETicketAction(Request $request) {
      $em = $this->getDoctrine()->getManager();
      // Find the eticket
      $eTicket = $em->getRepository('App\Entity\ETicket')->findOneById($request->request->get('eticket'));

      // Find the player or manager
      $participant = $em->getRepository('App\Entity\Player')->findOneByETicket($eTicket->getId());
      if ($participant == null) {
        $participant = $em->getRepository('App\Entity\TournamentManager')->findOneByETicket($eTicket->getId());
      }
      if ($participant == null) {
          $this->get('session')->getFlashBag()->add('error', "Pas de joueur ni de manager correspondant.");
        return $this->redirect($this->generateUrl("app_adminticketing_sent"));
      }

      // Check csrf token
      $submittedToken = $request->request->get('_token');
      if (!$this->isCsrfTokenValid('remove' . $eTicket->getId(), $submittedToken)) {
        $this->get('session')->getFlashBag()->add('error', "Token csrf invalide.");
        return $this->redirect($this->generateUrl("app_adminticketing_sent"));
      }



      $participant->setETicket(null);
      $eTicket->setStatus(ETicket::STATUS_CANCELLED);
      $em->persist($participant);
      $em->persist($eTicket);
      $em->flush();

      $this->sendCancelEmail($eTicket);

      $this->get('session')->getFlashBag()->add('info', "Billet annulé ");
      return $this->redirect($this->generateUrl("app_adminticketing_sent"));
    }

    /**
     * @Route("/ticketing/ticket/download")
     * @Method({"POST"})
     */
    public function downloadETicketAction(Request $request) {
      $em = $this->getDoctrine()->getManager();

      // Find the eticket
      $eTicket = $em->getRepository('App\Entity\ETicket')->findOneById($request->request->get('eticket'));

      // Check csrf token
      $submittedToken = $request->request->get('_token');
      if (!$this->isCsrfTokenValid('download' . $eTicket->getId(), $submittedToken)) {
        $this->get('session')->getFlashBag()->add('error', "Token csrf invalide.");
        return $this->redirect($this->generateUrl("app_adminticketing_ready"));
      }

      $pathName = realpath("").'/../data/ticket/'.$eTicket->getId().'.pdf';
      return $this->file($pathName);
    }

    private function sendETicket(ETicket $eTicket, $pdf) {
      $mailer = $this->get('mailer');
      // Create the message
      $message = (new \Swift_Message())
          ->setSubject('Votre inscription au tournoi ' . $eTicket->getTournament())
          ->setFrom(['noreply@insalan.fr' => 'InsaLan'])
          ->setTo([$eTicket->getUser()->getEmail()])
          ->setBody(
              $this->renderView(
                  'sendETicketEmail.html.twig',
                  ['user' => $eTicket->getUser(),
                   'tournament' => $eTicket->getTournament()
                 ]
              ),
              'text/html'
          );
      $message->attach(\Swift_Attachment::fromPath($pdf)->setFilename('Billet_InsaLan.pdf'));

      $mailer->send($message);

      $em = $this->getDoctrine()->getManager();
      $eTicket->setSentAt(new \DateTime("now"));
      $em->persist($eTicket);
      $em->flush();
      $this->get('session')->getFlashBag()->add('info', "Billet envoyé");
    }

    /**
     * @Route("/ticketing/sendall")
     */
    public function sendAllAction()
    {
      $em = $this->getDoctrine()->getManager();
      $tournaments = $em->getRepository('App\Entity\Tournament')->getUpcomingTournaments();
      $soloTournaments = $teamTournaments = array();
      foreach ($tournaments as $tournament) {
        if ($tournament->getParticipantType() == 'team') {
          $teamTournaments[] = $tournament;
        } else if ($tournament->getParticipantType() == 'player'){
          $soloTournaments[] = $tournament;
        }
      }
      $soloPlayers = $em->getRepository('App\Entity\Player')->findBy(["tournament" => $soloTournaments, "validated" => Participant::STATUS_VALIDATED, "eTicket" => null]);
      $teamPlayers = $em->getRepository('App\Entity\Player')->getValidatedTeamPlayers($teamTournaments);
      $players = array_merge($soloPlayers, $teamPlayers);

      // get validated managers
      $managers = array();
      $teams = $em->getRepository('App\Entity\TournamentTeam')->findBy(["tournament" => $teamTournaments, "validated" => Participant::STATUS_VALIDATED]);
      foreach ($soloPlayers as $player) {
        if ($player->getManager() && $player->getManager()->isOk() && $player->getManager()->getETicket() == null) {
          $managers[] = $player->getManager();
        }
      }
      foreach ($teams as $team) {
        if ($team->getManager() && $team->getManager()->isOk() && $team->getManager()->getETicket() == null) {
          $managers[] = $team->getManager();
        }
      }

      foreach($players as $participant) {
            // Check if a ticket already exists
            if (! $participant->getETicket()) {
                $tournament = $participant->getPendingRegistrable();
                // Generate pdf
                $pdf = $this->generateETicket($eTicket, $participant);
                $this->get('session')->getFlashBag()->add('info', "Billet créé ".$eTicket->getToken());
                $this->sendETicket($eTicket, $pdf);
            }
      }

      foreach($managers as $participant) {
            // Check if a ticket already exists
            if (! $participant->getETicket()) {
                $tournament = $participant->getTournament();
                // Generate pdf
                $pdf = $this->generateETicket($eTicket, $participant);
                $this->get('session')->getFlashBag()->add('info', "Billet créé ".$eTicket->getToken());
                $this->sendETicket($eTicket, $pdf);
            }
      }

      return $this->redirect($this->generateUrl("app_adminticketing_ready"));
    }

    private function generateETicket(ETicket $eTicket, $participant) {
      // TODO:
      try {
          $pathName = realpath("").'/../data/ticket/'.$eTicket->getId().'.pdf';
          $content = $this->renderView(
              'ticket.html.twig',
              ["user" => $eTicket->getUser(),
               "tournament" => $eTicket->getTournament(),
               "pseudo" => $participant->getGameName(),
               "type" => $participant->getParticipantType(),
               "managerPrice" => TournamentManager::ONLINE_PRICE,
               "eTicket" => $eTicket
              ]
            );
          $html2pdf = new Html2Pdf('P', 'A4', 'fr');
          $html2pdf->setDefaultFont('Arial');
          $html2pdf->writeHTML($content);
          $html2pdf->output($pathName, 'F');
          $this->get('session')->getFlashBag()->add('info', "PDF généré " . $pathName);
      } catch (Html2PdfException $e) {
          $html2pdf->clean();
          $formatter = new ExceptionFormatter($e);
          $this->get('session')->getFlashBag()->add('info', $formatter->getHtmlMessage());
      }
      return $pathName;
    }

    private function sendCancelEmail(ETicket $eTicket) {
      $mailer = $this->get('mailer');
      // Create the message
      $message = (new \Swift_Message())
          ->setSubject('Votre inscription au tournoi ' . $eTicket->getTournament())
          ->setFrom(['noreply@insalan.fr' => 'InsaLan'])
          ->setTo([$eTicket->getUser()->getEmail()])
          ->setBody(
              $this->renderView(
                  'cancelETicketEmail.html.twig',
                  ['user' => $eTicket->getUser(),
                   'tournament' => $eTicket->getTournament(),
                   'eticket' => $eTicket
                 ]
              ),
              'text/html'
          );

      $mailer->send($message);

      $this->get('session')->getFlashBag()->add('info', "Email envoyé");
    }
}
