<?php

namespace Application\EventListener;

use Application\Entity\UserActionEntity;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Silex\Application;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class SecurityListener implements EventSubscriberInterface
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function onInteractiveLogin($event)
    {
        $app = $this->app;
        $request = $app['request_stack']->getCurrentRequest();

        $token = $event->getAuthenticationToken();
        $user = $token->getUser();

        $userActionEntity = new UserActionEntity();
        $userActionEntity
            ->setUser($user)
            ->setKey('user.login')
            ->setMessage('User has been logged in!')
            ->setIp($request->getClientIp())
            ->setUserAgent($request->headers->get('User-Agent'))
        ;

        $app['orm.em']->persist($userActionEntity);
        $app['orm.em']->flush();
    }

    public function onSwitchUser($event)
    {
        $app = $this->app;
        $request = $app['request_stack']->getCurrentRequest();

        $user = $app['security.token_storage']->getToken()->getUser();
        $targetUser = $event->getTargetUser();

        if ($app['security.authorization_checker']->isGranted('ROLE_PREVIOUS_ADMIN')) {
            $targetUser = $app['orm.em']
                ->find(
                    'Application\Entity\UserEntity',
                    $targetUser->getId()
                )
            ;
            $userActionEntity = new UserActionEntity();
            $userActionEntity
                ->setUser($targetUser)
                ->setKey('user.switch.back')
                ->setMessage(
                    'User has switched back to own user (from user with ID "'.$user->getId().'")!'
                )
                ->setData([
                    'user_id' => $targetUser->getId(),
                    'from_user_id' => $user->getId(),
                ])
                ->setIp($request->getClientIp())
                ->setUserAgent($request->headers->get('User-Agent'))
            ;

            $app['orm.em']->persist($userActionEntity);
            $app['orm.em']->flush();
        } else {
            $userActionEntity = new UserActionEntity();
            $userActionEntity
                ->setUser($user)
                ->setKey('user.switch')
                ->setMessage(
                    'User has switched to user with ID "'.$targetUser->getId().'"!'
                )
                ->setData([
                    'user_id' => $user->getId(),
                    'to_user_id' => $targetUser->getId(),
                ])
                ->setIp($request->getClientIp())
                ->setUserAgent($request->headers->get('User-Agent'))
            ;

            $app['orm.em']->persist($userActionEntity);
            $app['orm.em']->flush();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => ['onInteractiveLogin'],
            SecurityEvents::SWITCH_USER => ['onSwitchUser'],
        ];
    }
}
