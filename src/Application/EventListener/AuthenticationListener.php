<?php

namespace Application\EventListener;

use Application\Entity\UserActionEntity;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Silex\Application;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
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
        $request = $app['request_stack']->getCurrentRequest();

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
            ->setData([
                'username' => $authenticationToken->getUser(),
            ])
            ->setIp($request->getClientIp())
            ->setUserAgent($request->headers->get('User-Agent'))
        ;

        if (!$user) {
            $userActionEntity
                ->setData(
                    [
                        'username' => $request->request->get('username'),
                    ]
                )
            ;
        }

        $app['orm.em']->persist($userActionEntity);

        $app['orm.em']->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            AuthenticationEvents::AUTHENTICATION_FAILURE => ['onAuthenticationFailure'],
        ];
    }
}
