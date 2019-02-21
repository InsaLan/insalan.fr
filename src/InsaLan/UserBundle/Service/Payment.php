<?php

namespace InsaLan\UserBundle\Service;

use InsaLan\UserBundle\Entity;

use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHumanStatus;
use Payum\Offline\PaymentFactory as OfflinePaymentFactory;
use Payum\Paypal\ExpressCheckout\Nvp\Api;

class Payment
{   

    private $payum;
    private $payumCheck;
    private $payumToken;
    private $paymentName = 'paypal_express_checkout_and_doctrine_orm';

    public function __construct($p, $pp, $ppp) {
        $this->payum = $p;
        $this->payumCheck = $pp;
        $this->payumToken = $ppp;
    }

    public function getOrder($currency, $price) {

        $storage =  $this->payum->getStorage('InsaLan\UserBundle\Entity\PaymentDetails');

        $order = $storage->create();

        $order['PAYMENTREQUEST_0_CURRENCYCODE'] = $currency;
        $order['PAYMENTREQUEST_0_AMT'] = $price;

        return $order;
    }

    public function getTargetUrl($order, $callbackRoute, $callbackParameters = null) {
        

        $storage =  $this->payum->getStorage('InsaLan\UserBundle\Entity\PaymentDetails');
        $storage->update($order);

        $payment = $this->payum->getGateway($this->paymentName);
        $captureToken = $this->payumToken->createCaptureToken(
            $this->paymentName,
            $order,
            $callbackRoute,
            $callbackParameters
        );

        $order['RETURNURL'] = $captureToken->getTargetUrl();
        $order['CANCELURL'] = $captureToken->getTargetUrl();
        $storage->update($order);

        return $captureToken->getTargetUrl();

    }

    public function check($request, $invalidate = false) {
        $token = $this->payumCheck->verify($request);
        $payment = $this->payum->getGateway($token->getGatewayName());
        
        if($invalidate)
            $this->payumCheck->invalidate($token);

        $payment->execute($status = new GetHumanStatus($token));

        return $status->isCaptured();

    }


}