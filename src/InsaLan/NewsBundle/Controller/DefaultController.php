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

        $news = $em->getRepository('InsaLanNewsBundle:News')->getLatest(20);
        $sliders = $em->getRepository('InsaLanNewsBundle:Slider')->getLatest(20);
        //$this->get('session')->getFlashBag()->add('info', 'Hey!');

        return array('news' => $news, 'sliders' => $sliders);
    }
}
