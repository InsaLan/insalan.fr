<?php

namespace InsaLan\CosplayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UserController extends Controller
{
    /**
     * @Route("/cosplay/register")
     * @Template()
     */
    public function registerAction()
    {
        return $this->render('InsaLanCosplayBundle:Default:index.html.twig');
    }
}
