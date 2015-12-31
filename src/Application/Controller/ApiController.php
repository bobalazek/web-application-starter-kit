<?php

namespace Application\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class ApiController
{
    /**
     * @param Request     $request
     * @param Application $app
     *
     * @return Response
     */
    public function indexAction(Request $request, Application $app)
    {
        return $app->json(array(
            'status' => 'success',
            'message' => 'Hello API!',
        ));
    }
}
