<?php

namespace Application\EventListener;

use Application\Entity\UserActionEntity;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Silex\Application;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class AuthenticationListener implements EventSubscriberInterface
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function onAuthenticationFailure($event)
    {
        $app = $this->app;

        $authenticationToken = $event->getAuthenticationToken();
        $user = $app['users.provider']->loadUserByUsername(
            $authenticationToken->getUser(),
            false
        );

        $userActionEntity = new UserActionEntity();
        $userActionEntity
            ->setUser($user)
            ->setKey('user.login.fail')
            ->setMessage('An user has tried to log in!')
            ->setData(array(
                'username' => $authenticationToken->getUser(),
            ))
            ->setIp($app['request']->getClientIp())
            ->setUserAgent($app['request']->headers->get('User-Agent'))
        ;

        if (!$user) {
            $userActionEntity
                ->setData(
                    array(
                        'username' => $app['request']->request->get('username'),
                    )
                )
            ;
        }

        $app['orm.em']->persist($userActionEntity);

        $app['orm.em']->flush();
    }

    public static function getSubscribedEvents()
    {
        return array(
            AuthenticationEvents::AUTHENTICATION_FAILURE => array('onAuthenticationFailure'),
        );
    }
}
