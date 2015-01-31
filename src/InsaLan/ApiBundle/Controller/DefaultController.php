<?php

namespace InsaLan\ApiBundle\Controller;

use InsaLan\ApiBundle\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/user/me")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();
        $player = $em->getRepository('InsaLanTournamentBundle:Player')->findByUser($usr);
        $traw = $request->query->get('tournaments');
        $t = explode(",", $traw);

        $res = array(
            "user" => array("username" => $usr->getUsername()),
            "err" => "registration_not_found"
            
        );
        foreach($player as $p) {
            if (in_array($p->getTournament()->getShortname(),$t))
                $res["err"] = "no_paid_place";
                if($p->getPaymentDone())
                    $res["err"] = null;

        }

        return new JsonResponse($res);
    }
}
