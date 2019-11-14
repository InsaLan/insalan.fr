<?php

namespace InsaLan\CosplayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use InsaLan\CosplayBundle\Entity\Cosplayer;
use InsaLan\CosplayBundle\Form\CosplayerType;

class UserController extends Controller
{
    /**
     * @Route("/cosplay/register")
     * @Template()
     */
    public function registerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $p1 = new Cosplayer();
        $form = $this->createForm(CosplayerType::class, $p1);
        if ($form->isSubmitted() && $form->isValid()) {
        	$em->persist($p1);
        	$em->flush();
        	return $this->redirect($this->generateUrl('homepage'));
        }
        return $this->render('InsaLanCosplayBundle:User:register.html.twig', array(
            'form' => $form->createView(),
            ));

    }
}
