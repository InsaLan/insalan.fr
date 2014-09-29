<?php

namespace InsaLan\UserBundle\Controller;

use InsaLan\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use GuzzleHttp\Exception\ClientException;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
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
        $user->setLolId($r_summoner->id);
        $user->setLolName($r_summoner->name);
        $user->setLolPicture($r_summoner->profileIconId);
        $user->setLolIdValidated(2);
        $logger->info('[STEP] 1 - Submitted summoner name : '.$r_summoner->id);

      } catch(\Exception $e) {
        $user->setLolIdValidated(-1);
        $logger->error('[STEP] 1 - '.$e->getMessage());
      }
      
      $em = $this->getDoctrine()->getManager();
      $em->persist($this->getUser());
      $em->flush();
      return $this->redirect($this->generateUrl('insalan_user_default_index'));
    }

    /**
     * @Route("/game-id/lol/reset")     
     * @Method({"GET"})
     */
    public function gameIdLolReset(Request $request) {
      $user = $this->getUser();

      $user->setLolId(null);
      $user->setLolName(null);

      $em = $this->getDoctrine()->getManager();
      $em->persist($this->getUser());
      $em->flush();
       
      return $this->redirect($this->generateUrl('insalan_user_default_index'));
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
      
      $user->setLolIdValidated(1);
      try {
        $mastery_pages = $api_summoner->masteryPages($user->getLolId());
        foreach ($mastery_pages as $page) {
          if ($page->get('name') == 'insalan'.$user->getId()) {
            $user->setLolIdValidated(0);
            break;
          }
          $logger->info('Loop '.$page->get('name'));
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

      } catch(\Exception $e) {
        $this->get('session')->getFlashBag()->add(
          'error',
          'Verifiez que la page de maitrise existe, et réessayez dans quelques secondes.'
        );
        $logger->error('[STEP] 2 - '.$e->getMessage());
      }


      return $this->redirect($this->generateUrl('insalan_user_default_index'));
    }

    /**
     * @Route("/game-id/lol/invalidate")     
     * @Method({"GET"})
     */
    public function gameIdLolInvalidate() {
      $user = $this->getUser();

      $user->setLolIdValidated(2);

      $em = $this->getDoctrine()->getManager();
      $em->persist($this->getUser());
      $em->flush();
       
      return $this->redirect($this->generateUrl('insalan_user_default_index'));

    }
}
