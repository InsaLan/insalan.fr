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
        $usr = $this->get('security.token_storage')->getToken()->getUser();

        $tournamentRaw = $request->query->get('tournaments');
        $t = explode(",", $tournamentRaw);

        $res = array(
            "user" => array("username" => $usr->getUsername()),
            "err" => "registration_not_found",
            "tournament" => null,
        );

        // look for a manager corresponding to user and provieded tournaments
        $manager = $em->getRepository('InsaLanTournamentBundle:Manager')->findByUser($usr);
        foreach ($manager as $m) {
            if ($m->getTournament())
                if (in_array($m->getTournament()->getShortname(),$t))
                    if($m->getPaymentDone()) {
                        $res["err"] = null;
                        $res["tournament"] = "manager";

                        return new JsonResponse($res);
                    } else {
                        $res["err"] = "no_paid_place";
                        // no return because we need to check if there is a player timezone_offset_get()
                    }
        }

        $player = $em->getRepository('InsaLanTournamentBundle:Player')->findByUser($usr);

        foreach($player as $p) {
            $res["err"] = "no_paid_place";

            if ($p->getTournament()) {
                if (in_array($p->getTournament()->getShortname(),$t))
                if($p->getPaymentDone()) {
                    $res["err"] = null;
                    $res["tournament"] = $p->getTournament()->getShortname();
                    return new JsonResponse($res);
                }
            }

            elseif ($p->getPendingRegistrable()) {
                if (in_array($p->getPendingRegistrable()->getShortname(),$t))
                if($p->getPaymentDone()) {
                    $res["err"] = null;
                    $res["tournament"] = $p->getPendingRegistrable()->getShortname();
                    return new JsonResponse($res);
                }
            }
        }

        return new JsonResponse($res);
    }

    /**
    * @Route("/user/2me")
    * @Template()
    * JSON structure:
    * {
	*   "user":{
	*   	"username",
	*   	"name",
	*   	"email"
	*   },
	*   "err",
	*   "tournament":[
	*   	{
	*   		"shortname",
	*   		"game_name",
    *   		"manager",
    *           "team", // Only for team players
	*   		"coached_participant", // Only for managers
	*   		"has_paid"
	*   	}
	*   ]
    * }
    */
    public function index2Action(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $usr = $this->get('security.token_storage')->getToken()->getUser();

      $res = array(
        "user" => array(
          "username" => $usr->getUsername(),
          "name" => $usr->getFirstname()." ".$usr->getLastname(),
          "email" => $usr->getEmail()
        ),
        "err" => "registration_not_found",
        "tournament" => null
        );


        // look for a manager corresponding to user
        $manager = $em->getRepository('InsaLanTournamentBundle:Manager')->findByUser($usr);
        foreach ($manager as $m) {
            if ($m->getTournament()->isPending() || $m->getTournament()->isPlaying()) {

                $tournament = [];
                $tournament["shortname"] =$m->getTournament()->getShortname();
                $tournament["game_name"] = $m->getGameName();
                $tournament["manager"] = True;
                if ($m->getParticipant()) {
                    $tournament["coached_participant"] = $m->getParticipant()->getName();
                }

                if($m->getPaymentDone()) {
                    $tournament["has_paid"] = True;
                    $res["err"] = null;
                } else {
                    $tournament["has_paid"] = False;
                }
                $res["tournament"][] = $tournament;
            } else {
                $res["err"] = "no_paid_place";
                // no return because we need to check if there is a player timezone_offset_get()
            }
        }

        $player = $em->getRepository('InsaLanTournamentBundle:Player')->findByUser($usr);

        foreach($player as $p) {
            $res["err"] = "no_paid_place";

            if ($p->getTournament()) {
                if ($p->getTournament()->isPending() || $p->getTournament()->isPlaying()) {

                    $tournament = [];
                    $tournament["shortname"] =$p->getTournament()->getShortname();
                    $tournament["game_name"] = $p->getGameName();
                    // Warning getTeam() returns a Collection
                    if ($p->getTeam()[0] != null) {
                        $tournament["team"] = $p->getTeam()[0]->getName();
                    }
                    $tournament["manager"] = False;

                    if($p->getPaymentDone()) {
                        $tournament["has_paid"] = True;
                        $res["err"] = null;
                    } else {
                        $tournament["has_paid"] = False;
                    }
                    $res["tournament"][] = $tournament;
                }
            }

            elseif ($p->getPendingRegistrable()) {
                if ($p->getPendingRegistrable()->isOpenedNow() || ($p->getPendingRegistrable()->getKind() == 'tournament' && ($p->getPendingRegistrable()->isPending() || $p->getPendingRegistrable()->isPlaying())) {

                    $tournament = [];
                    $tournament["shortname"] =$p->getPendingRegistrable()->getShortname();
                    $tournament["game_name"] = $p->getGameName();
                    // Warning getTeam() returns a Collection
                    if ($p->getTeam()[0] != null) {
                        $tournament["team"] = $p->getTeam()[0]->getName();
                    }
                    $tournament["manager"] = False;

                    if($p->getPaymentDone()) {
                        $tournament["has_paid"] = True;
                        $res["err"] = null;
                    } else {
                        $tournament["has_paid"] = False;
                    }
                    $res["tournament"][] = $tournament;
                }
            }
        }

        return new JsonResponse($res);
    }
}
