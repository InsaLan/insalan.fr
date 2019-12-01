<?php

namespace InsaLan\AdminBundle\Controller;

use InsaLan\TournamentBundle\Entity\Group;
use InsaLan\TournamentBundle\Entity\GroupStage;
use InsaLan\TournamentBundle\Entity\Match;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DefaultController extends Controller {
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction() {
        return array();
    }

    /**
     * @Route("/web")
     * @Template()
     */
    public function webAction() {
        return array();
    }
}
