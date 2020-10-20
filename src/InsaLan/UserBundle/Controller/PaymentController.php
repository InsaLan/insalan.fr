<?php

namespace InsaLan\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use InsaLan\TournamentBundle\Entity\Player;
use InsaLan\UserBundle\Entity\Discount;
use InsaLan\UserBundle\Entity\LyfpayOrder;

class PaymentController extends Controller
{

    /**
     * @Route("/pay/{registrable}/lyfpay_checkout")
     * @Route("/pay/{registrable}/discount/{discount}/lyfpay_checkout", requirements={"discount" = "\d+"})
     */
    public function lyfpayCheckoutAction(Entity\Registrable $registrable, $discount = null) {
        $em = $this->getDoctrine()->getManager();

        $usr = $this->get('security.token_storage')->getToken()->getUser();
        $player = $em
            ->getRepository('InsaLanTournamentBundle:Player')
            ->findOneByUserAndPendingRegistrable($usr, $registrable);

        $discount = $em->getRepository('InsaLanUserBundle:Discount')
                       ->findOneById($discount);

        if ($discount !== null && $discount->getRegistrable()->getId() !== $registrable->getId()){
            return $this->redirect($this->generateUrl('insalan_tournament_user_pay', array("registrable" => $registrable->getId())));
        }

        $order = new LyfpayOrder();
        $order->setUser($usr);
        $em->persist($order);
        $em->flush();

        $checkout_uri = $this->getParameter('lyfpay_checkout_uri');
        $key = $this->getParameter('lyfpay_checkout_secret_key');
        $lang = 'fr';
        $version = 'v2.0';
        $posUuid = $this->getParameter('lyfpay_checkout_pos_id');
        $shopReference = $order->getShopReference(); 
        $shopOrderReference = '';
        $amount = $registrable->getWebPrice() * 100; // amount must be in the smallest unit of the currency
        if ($discount !== null){
            $amount -= $discount->getAmount() * 100;
        }
        $deliveryFeesAmount = $registrable->getOnlineIncreaseInPrice() * 100; // TODO is this the right field ?
        $currency = $registrable->getCurrency(); 
        $mode = 'IMMEDIATE';
        $onSuccess = $this->generateUrl('insalan_user_payment_paydonesuccess', ['registrable' => $registrable->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $onError = $this->generateUrl('insalan_user_payment_paydoneerror', ['registrable' => $registrable->getId()], UrlGeneratorInterface::ABSOLUTE_URL); 
        $additionalData = json_encode(["callBackUrl" => "https://www.lyf.eu/callback"]); // TODO 
        $callBackRequired = true; 

        $message = $lang . '*' . $posUuid . '*' . $shopReference . '*' . $shopOrderReference . '*' . $deliveryFeesAmount .'*' . $amount . '*' . $currency . '*' . $onSuccess . '*' . $onError . '*' . $additionalData . '*' . $callBackRequired;
        $hash = hash_hmac("sha1", $message, $key);

        $parameters = [];
        $parameters['lang'] = $lang;
        $parameters['version'] = $version;
        $parameters['posUuid'] = $posUuid;
        $parameters['shopReference'] = $shopReference;
        $parameters['amount'] = $amount;
        $parameters['deliveryFeesAmount'] = $deliveryFeesAmount;
        $parameters['currency'] = $currency;
        $parameters['mode'] = $mode;
        $parameters['onSuccess'] = urlencode($onSuccess);
        $parameters['onError'] = urlencode($onError);
        $parameters['additionalDataEncoded'] = base64_encode($additionalData) ;
        $parameters['callBackRequired'] = $callBackRequired;
        $parameters['mac'] = $hash;

        return $this->redirect($checkout_uri, $parameters);
    }

    /**
     * @Route("/pay/{registrable}/done_success")
     */
    public function payDoneSuccessAction(Request $request, Entity\Registrable $registrable) {
        // TODO
    }

    /**
     * @Route("/pay/{registrable}/done_error")
     */
    public function payDoneErrorAction(Request $request, Entity\Registrable $registrable) {
        // TODO
    }

    /**
     * @Route("/pay/{registrable}/callback")
     */
    public function callbackAction(Request $request, Entity\Registrable $registrable) {
        // TODO
        $key = $this->getParameter('lyfpay_checkout_secret_key');
        $message = $_POST['posUuid'] . '*' . $_POST['shopReference'] . '*' . $_POST['shopOrderReference'] . '*' . $_POST['amount'] . '*'. $_POST['discount'] . '*' . $_POST['currency'] . '*' . $_POST['status'] . '*' . $_POST['creationDate'] . '*' . $_POST['transactionUuid'] . '*' . $_POST['additionalData'];
        $hash = hash_hmac("sha1", $message, $key);
        if( strcasecmp($_POST['mac'], $hash) == 0 ) {

            return new Response("OK", Response::HTTP_OK);
        }
    }

}
