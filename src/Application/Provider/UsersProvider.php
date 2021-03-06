<?php

namespace Application\Provider;

use Application\Entity\UserEntity;
use Silex\Application;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class UsersProvider implements UserProviderInterface
{
    private $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $username
     * @param bolean $showExceptionIfNotExistent
     *
     * @return UserEntity
     *
     * @throws UsernameNotFoundException If user was not found
     */
    public function loadUserByUsername($username, $showExceptionIfNotExistent = true)
    {
        $user = null;

        $userByUsername = $this->app['orm.em']
            ->getRepository('Application\Entity\UserEntity')
            ->findOneBy([
                'username' => $username,
            ])
        ;

        $userByEmail = $this->app['orm.em']
            ->getRepository('Application\Entity\UserEntity')
            ->findOneBy([
                'email' => $username,
            ])
        ;

        if ($userByUsername) {
            $user = $userByUsername;
        } elseif ($userByEmail) {
            $user = $userByEmail;
        }

        if (
            !$user &&
            $showExceptionIfNotExistent
        ) {
            throw new UsernameNotFoundException(
                sprintf(
                    'Username or Email "%s" does not exist.',
                    $username
                )
            );
        }

        return $user;
    }

    /**
     * @param string $accessToken
     * @param bolean $throwExceptionIfNotExistent
     *
     * @return UserEntity
     *
     * @throws UsernameNotFoundException If user was not found
     */
    public function loadUserByAccessToken($accessToken, $throwExceptionIfNotExistent = true)
    {
        $user = $this->app['orm.em']
            ->getRepository('Application\Entity\UserEntity')
            ->findOneBy([
                'accessToken' => $accessToken,
            ])
        ;

        if (
            !$user &&
            $throwExceptionIfNotExistent
        ) {
            throw new UsernameNotFoundException(
                'A user with this access token was not found.'
            );
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof UserEntity) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    get_class($user)
                )
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Application\Entity\UserEntity';
    }
}
