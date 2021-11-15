<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\Response;

class JsonResponse extends Response {
    public function __construct($data) {
        parent::__construct();
        $this->headers->set('Content-Type', 'application/json');
        $this->setContent(json_encode($data));
    }
}
