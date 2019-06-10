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

        // Generate QR code
        $this->generateQRCode($eTicket);

        // Generate pdf
        $globalKeys = ['fullDates'];
        $globalVars = $em->getRepository('InsaLanBundle:GlobalVars')->getGlobalVars($globalKeys);
        $pdf = $this->generateETicket($eTicket, $participant, $globalVars);
        $this->get('session')->getFlashBag()->add('info', "Billet créé ".$eTicket->getToken());

        $this->sendETicket($eTicket, $pdf);
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

      // Delete pdf
      $filesystem = new Filesystem();
      $eTicket = $participant->getETicket();
      $filesystem->remove([realpath("").'/../data/ticket/'.$eTicket->getId().'.pdf']);

      $eTicket = $participant->getETicket();
      $participant->setETicket(null);
      $em->persist($participant);
      $em->remove($eTicket);
      $em->flush();

      $this->get('session')->getFlashBag()->add('info', "Billet annulé ");
      return $this->redirect($this->generateUrl("insalan_ticketing_admin_index"));
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
                  'InsaLanTicketingBundle:Templates:email.html.twig',
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

    private function generateETicket(ETicket $eTicket, $participant, $globalVars) {
      // TODO:
      try {
          $pathName = realpath("").'/../data/ticket/'.$eTicket->getId().'.pdf';
          $content = $this->renderView(
              'InsaLanTicketingBundle:Templates:ticket.html.twig',
              ["user" => $eTicket->getUser(),
               "tournament" => $eTicket->getTournament(),
               "pseudo" => $participant->getGameName(),
               "token" => $eTicket->getToken(),
               "globalVars" => $globalVars
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

    private function generateQRCode(ETicket $eTicket) {
/*
      // Create a basic QR code
      $qrCode = new QrCode($eTicket->getToken());
      $qrCode->setSize(300);

      // Save it to a file
      $qrCode->writeFile(realpath("").'/../data/qrcode/'.$eTicket->getId().'.png');*/
    }
}
