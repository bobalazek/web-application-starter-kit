<?php

namespace Application\Controller\Api;

use Silex\Application;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class V1Controller
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
            'message' => 'Hello API v1!',
        ]);
    }
}
