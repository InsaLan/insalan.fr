<?php

namespace InsaLan\CosplayBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use InsaLan\CosplayBundle\Entity\Cosplayer;
use InsaLan\CosplayBundle\Form\CosplayerType;
use InsaLan\CosplayBundle\Entity\Cosplay;
use InsaLan\CosplayBundle\Form\CosplayType;

class UserController extends Controller
{
    /**
     * @Route("/cosplay/register")
     * @Template()
     */
    public function registerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $p1 = new Cosplayer();
        $form = $this->createForm(CosplayerType::class, $p1);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        	$em->persist($p1);
        	$em->flush();
        	return $this->redirect($this->generateUrl('insalan_cosplay_register'));
        }
        return $this->render('InsaLanCosplayBundle:User:register.html.twig', array(
            'form' => $form->createView(),
            ));

    }

    /**
     * @Route("/cosplay/groupeRegister")
     * @Template()
     */
    public function groupeRegisterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $p1 = new Cosplay();
        $count = new IntegerType;
        $form = $this->createForm(CosplayType::class, $p1);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        	$em->persist($p1);
        	$em->flush();
        	return $this->redirect($this->generateUrl('insalan_cosplay_index'));
        }
        return $this->render('InsaLanCosplayBundle:User:groupeRegister.html.twig', array(
            'form' => $form->createView(),
            ));
    }
}
