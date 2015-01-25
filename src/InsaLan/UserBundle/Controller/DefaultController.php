<?php

namespace InsaLan\UserBundle\Controller;

use InsaLan\UserBundle\Entity\User;
use InsaLan\UserBundle\Exception\ControllerException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use InsaLan\UserBundle\Form\UserType;

class DefaultController extends Controller
{   

    /**
     * @Route("/")
     * @Template("InsaLanUserBundle:Default:more.html.twig")
     */
    public function indexAction(Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new UserType(), $usr);
        $em = $this->getDoctrine()->getManager();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($usr);
            $em->flush();
            return $this->redirect($this->generateUrl('insalan_tournament_user_index'));
        }

        return array('form' => $form->createView());
    }

    
}
