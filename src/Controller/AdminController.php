<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\TournamentGroupStage;
use App\Entity\TournamentMatch;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
/**
 * @Route("/admin")
 */
class AdminController extends Controller {
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction() {
        return $this->render('Admin/adminIndex.html.twig');
    }

    /**
     * @Route("/web")
     * @Template()
     */
    public function webAction() {
        return $this->render('Admin/adminWeb.html.twig');
    }
}
