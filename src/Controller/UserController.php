<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\UserControllerException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Form\UserType;
use Ehesp\SteamLogin\SteamLogin;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use OAuth2\Client;
use OAuth2\GrantType\IGrantType;
use OAuth2\GrantType\AuthorizationCode;

/**
 * @Route("/user")
 */
class UserController extends Controller
{

    /**
     * @Route("/")
     * @Template("userMore.html.twig")
     */
    public function indexAction(Request $request)
    {
        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $form = $this->createForm('App\Form\UserType', $usr);
        $em = $this->getDoctrine()->getManager();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($usr);
            $em->flush();
            return $this->redirect($this->generateUrl('app_tournamentuser_index'));
        }

        $battletag = $usr->getBattleTag();
        $steam = null;

        if ($usr->getSteamId() != null) {
            $steam = $this->get("insalan.user.login_platform")->getSteamDetails($usr);
        }
        return $this->render('userMore.html.twig', ['form' => $form->createView(), 'steam' => $steam, 'battletag' => $battletag,]);
    }
    /**
     * @Route("/steamSignin",)
     * @Template("userSteamRegistration.html.twig")
     */
    public function registerSteamIdAction(Request $request)
    {
        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $connectedAccount = $usr->getSteamId();
        $imageSrc = null;
        $routeDelete = null;
        $login = new SteamLogin();
        $url = $this->generateUrl('app_user_savesteamid', array('slug' => ''),UrlGeneratorInterface::ABSOLUTE_URL);
        $url = str_replace("http://", "https://", $url);
        $url = $login->url($url);

        if($request->query->get('action') == 'remove') {
            $em = $this->getDoctrine()->getManager();
            $usr->setSteamId(null);
            $em->persist($usr);
            $em->flush();
            return $this->redirect($this->generateUrl('app_user_registersteamid'));
        }

        if($connectedAccount != null) {
            $routeDelete = $this->generateUrl('app_user_registersteamid');
            $routeDelete = $routeDelete.'?action=remove';
            $steamDetails = $this->get("insalan.user.login_platform")->getSteamDetails($usr);
            $connectedAccount = $steamDetails->personaname;
            $imageSrc = $steamDetails->avatar;
        }

        return $this->render('userSteamRegistration.html.twig', ['url' => $url, 'connectedAccount' => $connectedAccount, 'avatarSteamSrc' => $imageSrc,"deleteLink" => $routeDelete]);
    }

    /**
     * @Route("/steamSignin/sent",)
     * @Template("userSteamRegistrationSent.html.twig")
     */
    public function saveSteamIdAction(Request $request)
    {
        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $login = new SteamLogin();
        try {
            $id = $login->validate(100);
        } catch (\Exception $e) {
            return $this->render('userSteamRegistrationSent.html.twig', ['erreur' => true]);
        }
        $em = $this->getDoctrine()->getManager();
        $callbackRoute = $this->get('session')->get('callbackRegisterApiRoute');
        $callbackParams = $this->get('session')->get('callbackRegisterApiParams');

        if(isset($id)) {
            $usr->setSteamId($id);
            $em->persist($usr);
            $em->flush();
            if($callbackRoute != null) {
                $this->get('session')->set('callbackRegisterApiRoute', null);
                $this->get('session')->set('callbackRegisterApiParams', null);

                return $this->redirect($this->generateUrl($callbackRoute,$callbackParams));
            }

            $steamDetails = $this->get("insalan.user.login_platform")->getSteamDetails($usr);
            $connectedAccount = $steamDetails->personaname;
            $imageSrc = $steamDetails->avatarmedium;
            return array('connectedAccount' => $connectedAccount, 'avatarSteamSrc' => $imageSrc);
        }

        return array('erreur' => true);
    }

    /**
     * @Route("/battleNetSignIn",)
     * @Template("userBattleNetRegistration.html.twig")
     */
    public function registerBattleNetAction(Request $request)
    {
        $usr = $this->get('security.token_storage')->getToken()->getUser();

        $client_id       = $this->getParameter('battlenet_api_key');
        $client_secret   = $this->getParameter('battlenet_api_secret');
        $state           = 'signin';
        $scope           = '';
        $redirect_uri    = $this->getParameter('battlenet_api_redirect_uri');
        $authorize_uri   = 'https://eu.battle.net/oauth/authorize';
        $token_uri       = 'https://eu.battle.net/oauth/token';

        if(isset($_GET['action']) && $_GET['action'] == 'remove') {
            $em = $this->getDoctrine()->getManager();
            $usr->setBattleTag(null);
            $em->persist($usr);
            $em->flush();
            unset($_GET['code']); // just to be sure
        }
        $client = new Client($client_id, $client_secret);
        if($usr->getBattleTag() != null) {
            return $this->render('userBattleNetRegistration.html.twig', ['enregistre' => true, 'battletag' => $usr->getBattleTag()]);
        }
        if (!isset($_GET['code'])) {
            $auth_url = $client->getAuthenticationUrl($authorize_uri, $redirect_uri);
            return $this->render('userBattleNetRegistration.html.twig', ['enregistre' => false, 'url' => $auth_url]);
        } else {
            $code = $_GET['code'];
            $params = array('code' => $code, 'redirect_uri' => $redirect_uri);
            try {
                $response = $client->getAccessToken($token_uri, 'authorization_code', $params);
                $info = $response['result'];
                if(isset($info) && isset($info['access_token'])) {
                    $client->setAccessToken($info['access_token']);
                    $response = $client->fetch('https://eu.battle.net/oauth/userinfo');
                    $em = $this->getDoctrine()->getManager();
                    $usr->setBattleTag($response['result']['battletag']);
                    $usr->setBattleTagUpdatedAt(new \DateTime("now"));
                    $em->persist($usr);
                    $em->flush();
                    return $this->render('userBattleNetRegistration.html.twig', ['enregistre' => true, 'battletag' => $usr->getBattleTag()]);
                } else {
                    return $this->render('userBattleNetRegistration.html.twig', ['erreur' => true]);
                }
            } catch(\Exception $e) {
                    return $this->render('userBattleNetRegistration.html.twig', ['erreur' => true]);
            }
        }
        return null;
    }

}
