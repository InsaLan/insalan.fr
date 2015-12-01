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
            "err" => "registration_not_found",
            "tournament" => null
            
        );

        foreach($player as $p) {

            $res["err"] = "no_paid_place";

            if ($p->getTournament()) {
                if (in_array($p->getTournament()->getShortname(),$t))
                if($p->getPaymentDone()) {
                    $res["err"] = null;
                    $res["tournament"] = $p->getTournament()->getShortname();
                    continue;
                }
            }

            elseif ($p->getPendingTournament()) {
                if (in_array($p->getPendingTournament()->getShortname(),$t))
                if($p->getPaymentDone()) {
                    $res["err"] = null;
                    $res["tournament"] = $p->getPendingTournament()->getShortname();
                    continue;
                }
            }
            

        }

        return new JsonResponse($res);
    }
}
