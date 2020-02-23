<?php

namespace InsaLan\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use InsaLan\NewsBundle\Entity;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $news = $em->getRepository('InsaLanNewsBundle:News')->getLatest(1);
        $sliders = $em->getRepository('InsaLanNewsBundle:Slider')->getLatest(20);

        if ($this->container->getParameter('kernel.environment') === 'dev') {
            $this->get('session')->getFlashBag()->add('info', 'Debug: info flashbag');
            $this->get('session')->getFlashBag()->add('error', 'Debug: error flashbag');
        }

        // Get global variables
        $globalVars = array();
        $globalKeys = ['fullDates', 'romanNumber'];
        $globalVars = $em->getRepository('InsaLanBundle:GlobalVars')->getGlobalVars($globalKeys);

        return array('news' => $news, 'sliders' => $sliders, 'globalVars' => $globalVars);
    }

     /**
     * @Route("/forum/")
     */
    public function forumAction()
    {
        return $this->redirect("http://old.insalan.fr/forum");
    }
}
