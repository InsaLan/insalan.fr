<?php

namespace InsaLan\TicketingBundle\Controller;

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

use InsaLan\TournamentBundle\Entity\Player;
use InsaLan\TournamentBundle\Entity\Manager;
use InsaLan\TournamentBundle\Entity\Tournament;
use InsaLan\TournamentBundle\Entity\Participant;
use InsaLan\TicketingBundle\Entity\ETicket;

class AdminController extends Controller
{
    /**
     * @Route("/admin/ready")
     * @Template()
     */
    public function readyAction()
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
      $soloPlayers = $em->getRepository('InsaLanTournamentBundle:Player')->findBy(["tournament" => $soloTournaments, "validated" => Participant::STATUS_VALIDATED, "eTicket" => null]);
      $teamPlayers = $em->getRepository('InsaLanTournamentBundle:Player')->getValidatedTeamPlayers($teamTournaments);
      $players = array_merge($soloPlayers, $teamPlayers);

      // get validated managers
      $managers = array();
      $teams = $em->getRepository('InsaLanTournamentBundle:Team')->findBy(["tournament" => $teamTournaments, "validated" => Participant::STATUS_VALIDATED]);
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
     * @Route("/admin/sent")
     * @Template()
     */
    public function sentAction()
    {
      $em = $this->getDoctrine()->getManager();
      $eTickets = $em->getRepository('InsaLanTicketingBundle:ETicket')->findByStatus([ETicket::STATUS_VALID, ETicket::STATUS_SCANNED]);
      return array("eTickets" => $eTickets);
    }

    /**
     * @Route("/admin/cancelled")
     * @Template()
     */
    public function cancelledAction()
    {
      $em = $this->getDoctrine()->getManager();
      $eTickets = $em->getRepository('InsaLanTicketingBundle:ETicket')->findByStatus(ETicket::STATUS_CANCELLED);
      return array("eTickets" => $eTickets);
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
          return $this->redirect($this->generateUrl("insalan_ticketing_admin_ready"));
        }

        // Check csrf token
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('send' . $participant->getId(), $submittedToken)) {
          $this->get('session')->getFlashBag()->add('error', "Token csrf invalide.");
          return $this->redirect($this->generateUrl("insalan_ticketing_admin_ready"));
        }

        // Check if a ticket already exists
        if ($participant->getETicket()) {
          $this->get('session')->getFlashBag()->add('error', "Billet déjà créé ");
          return $this->redirect($this->generateUrl("insalan_ticketing_admin_ready"));
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
          return $this->redirect($this->generateUrl("insalan_ticketing_admin_ready"));
        }

        // Generate pdf
        $pdf = $this->generateETicket($eTicket, $participant);
        $this->get('session')->getFlashBag()->add('info', "Billet créé ".$eTicket->getToken());

        $this->sendETicket($eTicket, $pdf);
        return $this->redirect($this->generateUrl("insalan_ticketing_admin_ready"));
    }

    /**
     * @Route("/admin/ticket/remove")
     * @Method({"POST"})
     */
    public function removeETicketAction(Request $request) {
      $em = $this->getDoctrine()->getManager();
      // Find the eticket
      $eTicket = $em->getRepository('InsaLanTicketingBundle:ETicket')->findOneById($request->request->get('eticket'));

      // Find the player or manager
      $participant = $em->getRepository('InsaLanTournamentBundle:Player')->findOneByETicket($eTicket->getId());
      if ($participant == null) {
        $participant = $em->getRepository('InsaLanTournamentBundle:Manager')->findOneByETicket($eTicket->getId());
      }
      if ($participant == null) {
          $this->get('session')->getFlashBag()->add('error', "Pas de joueur ni de manager correspondant.");
        return $this->redirect($this->generateUrl("insalan_ticketing_admin_sent"));
      }

      // Check csrf token
      $submittedToken = $request->request->get('_token');
      if (!$this->isCsrfTokenValid('remove' . $eTicket->getId(), $submittedToken)) {
        $this->get('session')->getFlashBag()->add('error', "Token csrf invalide.");
        return $this->redirect($this->generateUrl("insalan_ticketing_admin_sent"));
      }



      $participant->setETicket(null);
      $eTicket->setStatus(ETicket::STATUS_CANCELLED);
      $em->persist($participant);
      $em->persist($eTicket);
      $em->flush();

      $this->sendCancelEmail($eTicket);

      $this->get('session')->getFlashBag()->add('info', "Billet annulé ");
      return $this->redirect($this->generateUrl("insalan_ticketing_admin_sent"));
    }

    /**
     * @Route("/admin/ticket/download")
     * @Method({"POST"})
     */
    public function downloadETicketAction(Request $request) {
      $em = $this->getDoctrine()->getManager();

      // Find the eticket
      $eTicket = $em->getRepository('InsaLanTicketingBundle:ETicket')->findOneById($request->request->get('eticket'));

      // Check csrf token
      $submittedToken = $request->request->get('_token');
      if (!$this->isCsrfTokenValid('download' . $eTicket->getId(), $submittedToken)) {
        $this->get('session')->getFlashBag()->add('error', "Token csrf invalide.");
        return $this->redirect($this->generateUrl("insalan_ticketing_admin_ready"));
      }

      $pathName = realpath("").'/../data/ticket/'.$eTicket->getId().'.pdf';
      return $this->file($pathName);
    }

    private function sendETicket(ETicket $eTicket, $pdf) {
      $mailer = $this->get('mailer');
      // Create the message
      $message = (new \Swift_Message())
          ->setSubject('Votre inscription au tournoi ' . $eTicket->getTournament())
          ->setFrom(['contact@insalan.fr' => 'InsaLan'])
          ->setTo([$eTicket->getUser()->getEmail()])
          ->setBody(
              $this->renderView(
                  'InsaLanTicketingBundle:Templates:sendETicketEmail.html.twig',
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

    private function generateETicket(ETicket $eTicket, $participant) {
      // TODO:
      try {
          $pathName = realpath("").'/../data/ticket/'.$eTicket->getId().'.pdf';
          $content = $this->renderView(
              'InsaLanTicketingBundle:Templates:ticket.html.twig',
              ["user" => $eTicket->getUser(),
               "tournament" => $eTicket->getTournament(),
               "pseudo" => $participant->getGameName(),
               "type" => $participant->getParticipantType(),
               "managerPrice" => Manager::ONLINE_PRICE,
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
          ->setFrom(['contact@insalan.fr' => 'InsaLan'])
          ->setTo([$eTicket->getUser()->getEmail()])
          ->setBody(
              $this->renderView(
                  'InsaLanTicketingBundle:Templates:cancelETicketEmail.html.twig',
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
