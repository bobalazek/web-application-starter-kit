<?php

namespace Application\Controller;

use Silex\Application;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class ApiController
{
    /**
     * @param Application $app
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function indexAction(Application $app)
    {
        return $app->json([
            'status' => 'success',
            'message' => 'Hello API!',
        ]);
    }
}
