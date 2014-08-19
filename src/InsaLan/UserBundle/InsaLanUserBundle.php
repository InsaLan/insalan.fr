<?php

namespace InsaLan\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class InsaLanUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
