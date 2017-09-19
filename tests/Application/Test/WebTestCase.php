<?php

namespace Application\Test;

use Silex\WebTestCase as SilexWebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class WebTestCase extends SilexWebTestCase
{
    /**
     * @return \Silex\Application
     */
    public function createApplication()
    {
        $app = require dirname(__FILE__).'/../../../app/bootstrap.php';

        $app['debug'] = false;
        $app['exception_handler']->disable();
        $app['session.test'] = true;

        return $app;
    }

    /**
     * @return \Symfony\Component\HttpKernel\Client
     */
    public function doLogin($username, array $roles = [])
    {
        $app = $this->createApplication();
        $client = $this->createClient();
        $client->followRedirects();

        $session = $app['session'];
        $firewall = 'members-area';

        $token = new UsernamePasswordToken($username, null, $firewall, $roles);
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        return $client;
    }
}
