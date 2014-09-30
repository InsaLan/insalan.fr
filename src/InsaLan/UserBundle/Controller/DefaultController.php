<?php

namespace InsaLan\UserBundle\Controller;

use InsaLan\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use GuzzleHttp\Exception\ClientException;
use InsaLan\TournamentBundle\Entity\Player;
use InsaLan\TournamentBundle\Entity\Team;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
      $user = $this->getUser();   
      if ($user->getPlayer() !== null 
          && $user->getPlayer()->getTeam() !== null 
          && $user->getPlayer()->getTeam()->getValidated()) 
      {
        return array();
        } else {  
        return $this->redirect($this->generateUrl('insalan_user_default_join'));
      }
    }

    /**
     * @Route("/join")
     * @Template()
     */
    public function joinAction()
    {
        return array();
    }

    /**
     * @Route("/game-id/lol/set")     
     * @Method({"POST"})
     */
    public function gameIdLolSet(Request $request) {
      $name = $request->request->get('summoner');
      $api_lol = $this->container->get('insalan.lol');
      $api_summoner = $api_lol->getApi()->summoner();
      $user = $this->getUser();

      $logger = $this->get('logger');
      
      try {
        $r_summoner = $api_summoner->info($name);
        $user->setPlayer(new Player());
        $user->getPlayer()->setLolId($r_summoner->id);
        $user->getPlayer()->setName($r_summoner->name);
        $user->getPlayer()->setLolPicture($r_summoner->profileIconId);
        $user->getPlayer()->setLolIdValidated(false);
        $em = $this->getDoctrine()->getManager();
        $em->persist($this->getUser());
        $em->flush();
        $logger->info('[STEP] 1 - Submitted summoner name : '.$r_summoner->id);

      } catch(\Exception $e) {
        $this->get('session')->getFlashBag()->add(
            'errorStep1',
            $e->getMessage()
        );
        $logger->error('[STEP] 1 - '.$e->getMessage());
      }
      
      return $this->redirect($this->generateUrl('insalan_user_default_join'));
    }

    /**
     * @Route("/game-id/lol/reset")     
     * @Method({"GET"})
     */
    public function gameIdLolReset(Request $request) {
      $user = $this->getUser();
      $p = $user->getPlayer();
      $user->removePlayer();
      
      $em = $this->getDoctrine()->getManager();
      $em->persist($user);
      $em->remove($p);
      $em->flush();
       
      return $this->redirect($this->generateUrl('insalan_user_default_join'));
    }

    /**
     * @Route("/game-id/lol/validate")     
     * @Method({"POST"})
     */
    public function gameIdLolValidate() { 
      $api_lol = $this->container->get('insalan.lol');
      $api_summoner = $api_lol->getApi()->summoner();
      $user = $this->getUser();   
      $logger = $this->get('logger');
      
      try {
        $mastery_pages = $api_summoner->masteryPages($user->getPlayer()->getLolId());
        foreach ($mastery_pages as $page) {
          if ($page->get('name') == 'insalan'.$user->getId()) {
            $user->getPlayer()->setLolIdValidated(true);
            break;
          }
        }

        if(!$user->getPlayer()->getLolIdValidated()) { throw new \Exception('Can\'t found mastery page'); }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

      } catch(\Exception $e) {
        $this->get('session')->getFlashBag()->add(
          'errorStep2',
          $e->getMessage()
        );
        $logger->error('[STEP] 2 - '.$e->getMessage());
      }


      return $this->redirect($this->generateUrl('insalan_user_default_join'));
    }

    /**
     * @Route("/game-id/lol/invalidate")     
     * @Method({"GET"})
     */
    public function gameIdLolInvalidate() {
      $user = $this->getUser();

      $user->getPlayer()->setLolIdValidated(false);

      $em = $this->getDoctrine()->getManager();
      $em->persist($this->getUser());
      $em->flush();
       
      return $this->redirect($this->generateUrl('insalan_user_default_join'));

    }

    /**
     * @Route("/team-id/lol/select")
     * @Method({"POST"})
     */
    public function teamIdLolSelect(Request $request) {
      $user = $this->getUser();

      $logger = $this->get('logger');

      $name = $request->request->get('name');
      $password = $request->request->get('password');

      $teamRepo = $this->getDoctrine()->getRepository('InsaLanTournamentBundle:Team');

      $em = $this->getDoctrine()->getManager();

      try {
        if($user->getPlayer()->getTeam() !== null) throw new \Exception('User already in a team');

        $team = $teamRepo->findOneByName($name);
        if(!$team) {
          $team = new Team();
          $team->setName($name);
          $team->setPassword($password);

          $user->getPlayer()->setTeam($team);

        }
        else {
          if($password !== $team->getPassword()) throw new \Exception('Invalid password');
          if($team->getPlayers()->count() >= 5) throw new \Exception('No more free slot in this team');
          
          $user->getPlayer()->setTeam($team);
        }

        $user->getPlayer()->getTeam()->validate();

        $em->persist($user);
        $em->persist($user->getPlayer()->getTeam());
        $em->flush();

      } catch(\Exception $e) {
        $this->get('session')->getFlashBag()->add(
          'errorStep3',
          $e->getMessage()
        );
        $logger->error('[STEP] 3 - '.$e->getMessage());
      }

      return $this->redirect($this->generateUrl('insalan_user_default_join'));

    }

    /**
     * @Route("/team-id/lol/leave")
     * @Method({"GET"})
     */
    
    public function teamIdLolLeave() {
      $em = $this->getDoctrine()->getManager();
      $user = $this->getUser();
      $team = $user->getPlayer()->getTeam();

      $user->getPlayer()->setTeam(null);

      $em->persist($user);
      $em->flush();

      //Should we destroy the team in DB ?
      if($team->getPlayers()->count() === 0) {
        $em->remove($team);
      } else {
        $em->persist($team);
      }

      $em->flush();

      return $this->redirect($this->generateUrl('insalan_user_default_join'));
    }

}
