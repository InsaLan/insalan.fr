<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class OpeningRequestMatcher implements RequestMatcherInterface
{
    public function matches(Request $request)
    {
        $date = strtotime(\App\Controller\InsalanController::OPENING_DATE);

        if (time() >= $date) {
            return false;
        }

        $path = $request->getPathInfo();
        return (strpos($path, '/register') === 0);
    }
}
