<?php

namespace Application\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController
{
    public function indexAction(Request $request, Application $app)
    {
        $data = array(
            'status' => 'ok',
            'status_code' => 200,
            'message' => 'Hello API!',
        );

        return $app->json(
            $data
        );
    }
}
