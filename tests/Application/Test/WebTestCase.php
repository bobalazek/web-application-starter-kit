<?php

namespace Application\Test;

use Silex\WebTestCase as SilexWebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Application\Entity\UserEntity;

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
        $app['session.test'] = true;
        unset($app['exception_handler']);

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

        $user = new UserEntity();
        $user
            ->setEmail($username.'@email.com')
            ->setUsername($username)
            ->setRoles($roles);

        $token = new UsernamePasswordToken($user, null, $firewall, $roles);
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        return $client;
    }
}
