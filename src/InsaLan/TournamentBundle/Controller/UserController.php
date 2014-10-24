<?php

namespace InsaLan\TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use InsaLan\TournamentBundle\Form\SetLolPlayerType;
use InsaLan\TournamentBundle\Form\TeamType;
use InsaLan\TournamentBundle\Form\TeamLoginType;

use InsaLan\TournamentBundle\Entity\Player;
use InsaLan\TournamentBundle\Entity\Team;
use InsaLan\TournamentBundle\Entity;

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
     * @Route("/user/player/set/{game}/for_tournament/{tournamentId}")
     * @Template()
     */
    public function setPlayerAction(Request $request, $game, $tournamentId) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em->getRepository('InsaLanTournamentBundle:Player')->findOneByUser($usr->getId());
        if ($player === null) {
            $player = new Player();
            $player->setUser($usr);
        } 

        if ($game === 'lol') {
            return $this->lolSet($em,$usr,$player,$request, $tournamentId);
        }
        return array('selectedGame' => $game, 'tournamentId' => $tournamentId);

    }

    /**
     * @Route("/user/player/validate/{game}/for_tournament/{tournamentId}")
     * @Template()
     */
    public function validatePlayerAction($game, $tournamentId) {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em->getRepository('InsaLanTournamentBundle:Player')->findOneByUser($usr->getId());

        if ($player === null) {
            return $this->redirect($this->generateUrl('insalan_tournament_user_setplayer', array('game' => $game)));
        } else if ($game === 'lol') {
            return $this->lolValidation($em, $usr, $player, $tournamentId);
        } else {
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        } 
    }

    /**
     * @Route("/user/join/{id}/team")
     * @Template()
     */
    public function joinTeamAction($id)
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

        // Check if there is a player associated to this user
        if ($player === null || !$player->isNamed($tournament->getType())) {
            return $this->redirect(
                $this->generateUrl(
                    'insalan_tournament_user_setplayer',
                    array(
                        'game' => $tournament->getType(),
                        'tournamentId' => $id
                    )));
        } 
        // Check if this player is validated for the game
        else if (!$player->isValidated($tournament->getType())) {
            return $this->redirect(
                $this->generateUrl(
                    'insalan_tournament_user_validateplayer', 
                    array(
                        'game' => $tournament->getType(),
                        'tournamentId' => $id
                    )));
        } else if ($player->isRegisteredForTournament($id)) {
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        }

        return array('tournament' => $tournament, 'user' => $usr, 'player' => $player);
    }

    /**
     * @Route("/user/leave/team/{teamId}")
     * @Template()
     */
    public function leaveTeamAction($teamId) {
        $em = $this->getDoctrine()->getManager();
        $team = $em
            ->getRepository('InsaLanTournamentBundle:Team')
            ->findOneById($teamId);
        $usr = $this
            ->get('security.context')
            ->getToken()
            ->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUser($usr->getId());

        $player->leaveTeam($team);
        $em->persist($player);
        $em->persist($team);
        $em->flush();
        return $this->redirect($this->generateUrl('insalan_tournament_user_index'));

    }

    /**
     * @Route("/user/join/{id}/player")
     * @Template()
     */
    public function joinPlayerAction($id)
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

    /**
     * @Route("/user/join/{id}/team/create")
     * @Template()
     */
    public function createTeamAction(Request $request, $id) {
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

        $team = new Team();

        $form = $this->createForm(new TeamType(), $team);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($usr);
            $team->setPassword($encoder->encodePassword($team->getPlainPassword(), sha1('pleaseHashPasswords'.$team->getName())));
            $team->setTournament($tournament);
            $player->joinTeam($team);
            $em->persist($team);
            $em->persist($player);
            $em->flush();
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        }

        return array('tournament' => $tournament, 'user' => $usr, 'player' => $player, 'form' => $form->createView());
    }

    /**
     * @Route("/user/join/{id}/team/existing")
     * @Template()
     */
    public function existingTeamAction(Request $request, $id) {
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

        $team = new Team();

        $form = $this->createForm(new TeamLoginType(), $team);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($usr);
            $team->setPassword($encoder->encodePassword($team->getPlainPassword(), sha1('pleaseHashPasswords'.$team->getName())));
            $team2 = $em
                ->getRepository('InsaLanTournamentBundle:Team')
                ->findOneByName($team->getName());
            if ($team2 !== null && $team2->getPassword() === $team->getPassword()) {
                $player->joinTeam($team2);
                $em->persist($player);
                $em->persist($team);
                $em->flush();
                return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
            }

        }

        return array('tournament' => $tournament, 'user' => $usr, 'player' => $player, 'form' => $form->createView());
    }

    protected function lolSet($em, $usr, $player, $request, $tournamentId) {
        $form = $this->createForm(new SetLolPlayerType(), $player);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $player->setLolIdValidated(false);
            $em->persist($player);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('insalan_tournament_user_validateplayer', array('game'=>'lol', 'tournamentId' => $tournamentId))
            );
        }

        return array('form' => $form->createView(), 'selectedGame' => 'lol', 'tournamentId' => $tournamentId);
    }

    protected function lolValidation($em, $usr, $player, $tournamentId) {
        if ($player->getLolIdValidated()) {
            return $this->redirect(
                $this->generateUrl(
                    'insalan_tournament_user_jointeam', 
                    array(
                        'id' => $tournamentId
                    )));
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

            return array('player' => $player, 'error' => $details, 'selectedGame' => 'lol', 'tournamentId' => $tournamentId);
        }

    }

    /**
     * @Route("/user/team/{id}", requirements={"id" = "\d+"})
     * @Template()
     */
    public function teamDetailsAction(Entity\Team $team)
    {   
        $pvpService = $this->get('insalan.tournament.pvp_net');

        foreach ($team->getGroups() as $g)
        {
            $g->countWins();
            // TODO : LoL Only !
            foreach ($g->getMatches() as $m)
            {   
                $name = "INSALAN Match " . $m->getId();
                $m->pvpNetUrl = $pvpService->generateUrl(array("name" => $name));
            }
        }

        return array("team" => $team, "authorized" => $this->isUserInTeam($team));
    }

    /**
     * @Route("/user/public/team/{id}/validate/{match}", requirements={"id" = "\d+"})
     * @Template()
     */
    public function teamValidateMatchAction(Entity\Team $team, Entity\Match $match)
    {
        $pvpService = $this->get('insalan.tournament.pvp_net');

        if($match->getPart1() !== $team && $match->getPart2() !== $team)
            throw new \Exception("Invalid team");

        if(!$this->isUserInTeam($team))
            throw new \Exception("Invalid user");

        if($match->getState() != Entity\Match::STATE_ONGOING)
            throw new \Exception("Invalid match : not in ongoing state");

        $matchResult = $pvpService->getGameResult($match->getPart1(), $match->getPart2());

        $round = new Entity\Round();
        $round->setMatch($match);

        $round->setScore1(0);
        $round->setScore2(0);

        if($matchResult)
            $round->setScore1(1);

        else
            $round->setScore2(1);

        // TODO : not for LoL only

        $match->setState(Entity\Match::STATE_FINISHED);

        $em = $this->getDoctrine()->getManager();
        $em->persist($round);
        $em->persist($match);
        $em->flush();

        return $this->redirect($this->generateUrl('insalan_tournament_user_teamdetails', array('id' => $team->getId())));

    }

    /**
     * @Route("/user/team/{id}/addReplay/{round}", requirements={"id" = "\d+"})
     * @Template()
     */
    public function roundAddReplayAction(Request $request, Entity\Team $team, Entity\Round $round)
    {   

        // Check security
        if(!$this->isUserInTeam($team))
            throw new \Exception("Invalid user");

        if($round->getMatch()->getPart1()->getId() !== $team->getId()
            && $round->getMatch()->getPart2()->getId() !== $team->getId())
            throw new \Exception("Invalid round");

        if($round->getReplay() !== null)
            throw new \Exception("Le fichier a déjà été envoyé !");

        $form = $this->createFormBuilder($round)
            ->add('replayFile', 'file', array("label" => "Fichier"))
            ->add('save', 'submit', array("label" => "Ajouter le fichier"))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid())
        {   
            $em = $this->getDoctrine()->getManager();
            $em->persist($round);
            $em->flush();

            return $this->redirect($this->generateUrl('insalan_tournament_user_teamdetails', array('id' => $team->getId())));
        }

        return array("form" => $form->createView());
    }

    /** PRIVATE **/

    private function isUserInTeam(Entity\Team $team)
    {   

        $user = $this->get('security.context')->getToken()->getUser();

        foreach ($team->getPlayers() as $p)
        {
            if($p->getUser() !== null && $p->getUser()->getId() === $user->getId())
            {
                return true;
            }

        }

        return false;
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
                return;
            }
        }
        throw $this->createNotFoundException('La page de maîtrise n\'existe pas');
    }
}
