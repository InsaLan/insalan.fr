<?php

namespace InsaLan\CosplayBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use InsaLan\CosplayBundle\Entity\Cosplayer;
use InsaLan\CosplayBundle\Form\CosplayerType;
use InsaLan\CosplayBundle\Entity\Cosplay;
use InsaLan\CosplayBundle\Form\CosplayType;

class UserController extends Controller
{
    /**
     * @Route("/cosplay/register/{max}/{actuel}")
     * @Template()
     */
    public function registerAction(Request $request,int $max,int $actuel)
    {
        $em = $this->getDoctrine()->getManager();
        $p1 = new Cosplayer();
        $form = $this->createForm(CosplayerType::class, $p1);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        	$em->persist($p1);
            $em->flush();
            while ($actuel < $max){
                return $this->redirect($this->generateUrl('insalan_cosplay_user_register',array('max'=>$max, 'actuel' => $actuel+1 )));
            }
        	return $this->redirect($this->generateUrl('insalan_cosplay_default_index'));
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
        $count = 1;
        $form = $this->createForm(CosplayType::class, $p1);
        $form->handleRequest($request);
        if($_SERVER["REQUEST_METHOD"]  === 'POST'){
           $count = $_POST['count'];
        }
        if ($form->isSubmitted() && $form->isValid()) {
        	$em->persist($p1);
            $em->flush();
            return $this->redirect($this->generateUrl('insalan_cosplay_user_register',array('max' => $count,'actuel'=>1)));
        }
        return $this->render('InsaLanCosplayBundle:User:groupeRegister.html.twig', array(
            'form' => $form->createView(),
            ));
    }
}
