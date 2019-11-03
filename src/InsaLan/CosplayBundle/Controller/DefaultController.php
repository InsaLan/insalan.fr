<?php

namespace InsaLan\CosplayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/cosplay/index")
     */
    public function indexAction()
    {
        return $this->render('InsaLanCosplayBundle:Default:index.html.twig');
    }
}
