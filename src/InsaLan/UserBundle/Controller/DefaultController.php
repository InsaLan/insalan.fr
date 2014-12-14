<?php

namespace InsaLan\UserBundle\Controller;

use InsaLan\UserBundle\Entity\User;
use InsaLan\UserBundle\Exception\ControllerException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use GuzzleHttp\Exception\ClientException;
use InsaLan\TournamentBundle\Entity\Player;
use InsaLan\TournamentBundle\Entity\Team;
use InsaLan\TournamentBundle\Entity\Participant;

class DefaultController extends Controller
{   
    /**
     * DEPRECATED ?
     */

    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('insalan_user_default_join'));
    }

    /**
     * @Route("/join")
     * @Template()
     */
    public function joinAction()
    {
        return array();
    }

    
}
