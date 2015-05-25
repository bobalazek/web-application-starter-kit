<?php

namespace Application\Test;

use Silex\WebTestCase as SilexWebTestCase;

class WebTestCase
    extends SilexWebTestCase
{
    public function createApplication()
    {
        $app = require dirname(__FILE__).'/../../../app/bootstrap.php';

        $app['debug'] = false;
        $app['exception_handler']->disable();
        $app['session.test'] = true;

        return $app;
    }
}
