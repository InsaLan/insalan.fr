<?php
namespace InsaLan\InsaLanBundle\Security;

use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class OpeningRequestMatcher implements RequestMatcherInterface
{
    public function matches(Request $request)
    {
        $date = strtotime(\InsaLan\InformationBundle\Controller\DefaultController::OPENING_DATE);

        if (time() >= $date) {
            return false;
        }

        $path = $request->getPathInfo();
        return (strpos($path, '/register') === 0);
    }
}
