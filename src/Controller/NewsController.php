<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Entity;
/**
 * @Route("/")
 */
class NewsController extends Controller
{
    /**
     * @Route("/")
     * @Template("newsIndex.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $news = $em->getRepository("App\Entity\News")->getLatest(1);
        $sliders = $em->getRepository("App\Entity\NewsSlider")->getLatest(20);

        if ($this->container->getParameter("kernel.environment") === "dev") {
            $this->get("session")->getFlashBag()->add("info", "Debug: info flashbag");
            $this->get("session")->getFlashBag()->add("error", "Debug: error flashbag");
        }

        // Get global variables
        $globalVars = array();
        $globalKeys = ["fullDates", "romanNumber"];
        $globalVars = $em->getRepository("App\Entity\InsaLanGlobalVars")->getGlobalVars($globalKeys);
        return $this->render('newsIndex.html.twig', ["news" => $news, "sliders" => $sliders, "globalVars" => $globalVars]);
    }

     /**
     * @Route("/forum/")
     */
    public function forumAction()
    {
        return $this->redirect("http://old.insalan.fr/forum");
    }
}
