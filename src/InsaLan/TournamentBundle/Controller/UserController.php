<?php

namespace InsaLan\TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use InsaLan\TournamentBundle\Form\SetLolPlayerType;

use InsaLan\TournamentBundle\Entity\Player;

class UserController extends Controller
{
    /**
     * @Route("/user")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();

        $tournaments = $em->getRepository('InsaLanTournamentBundle:Tournament')->findOpened();
        $player = $em->getRepository('InsaLanTournamentBundle:Player')->findOneByUser($usr->getId());

        return array('tournaments' => $tournaments, 'player' => $player);
    }

    /**
     * @Route("/user/player/lol/set")
     * @Template()
     */
    public function setLolPlayerAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em->getRepository('InsaLanTournamentBundle:Player')->findOneByUser($usr->getId());
        if ($player === null) {
            $player = new Player();
            $player->setUser($usr);
        } 

        $form = $this->createForm(new SetLolPlayerType(), $player);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $player->setLolIdValidated(false);
            $em->persist($player);
            $em->flush();

            return $this->redirect($this->generateUrl('insalan_tournament_user_validatelolplayer'));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/user/player/lol/validate")
     * @Template()
     */
    public function validateLolPlayerAction() {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em->getRepository('InsaLanTournamentBundle:Player')->findOneByUser($usr->getId());

        if ($player === null) {
            return $this->redirect($this->generateUrl('insalan_tournament_user_setlolplayer'));
        } elseif ($player->getLolIdValidated()) {
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        } else {
            $details = null;
            try {
                $this->fetchInfo($usr, $player);
                $em->persist($player);
                $em->flush();
            } catch(\Exception $e) {
                $className = get_class($e);

                if ('GuzzleHttp\\Exception\\ClientException' === $className && 404 == $e->getResponse()->getStatusCode()) {
                    $details = 'Invocateur introuvable sur EUW';
                }
                else if (0 === strpos($className, 'GuzzleHttp')) {
                    $details = 'Erreur de l\'API. Veuillez réessayer.';
                } else {
                    $details = 'Une erreur inconnue est survenue';
                }
            }

            return array('player' => $player, 'error' => $details);
        }

    }

    /**
     * @Route("/user/join/{id}")
     * @Template()
     */
    public function joinAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $tournament = $em
            ->getRepository('InsaLanTournamentBundle:Tournament')
            ->findOneById($id);
        $usr = $this
            ->get('security.context')
            ->getToken()
            ->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUser($usr->getId());

        return array('tournament' => $tournament, 'user' => $usr, 'player' => $player);
    }

    protected function fetchInfo($user, $player) {
        $apiLol = $this->container->get('insalan.lol');
        $apiSummoner = $apiLol->getApi()->summoner();
        $rSummoner = $apiSummoner->info($player->getLolName());
        $player->setLolId($rSummoner->id);
        $player->setLolName($rSummoner->name);
        $player->setLolPicture($rSummoner->profileIconId);

        $masteryPages = $apiSummoner->masteryPages($player->getLolId());
        foreach ($masteryPages as $page) {
            if ($page->get('name') == 'insalan'.$user->getId()) {
                $player->setLolIdValidated(true);
                break;
            }
        }

    }
}
