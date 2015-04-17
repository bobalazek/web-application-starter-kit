<?php

namespace Application\Provider;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Application\Entity\UserEntity;

class UserProvider
    implements UserProviderInterface
{
    private $app;
    private $credentials;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    public function loadUserByUsername($username, $showExceptionIfNotExistent = true)
    {
        $user = null;

        $userByUsername = $this->app['orm.em']
            ->getRepository(
                'Application\Entity\UserEntity'
            )
            ->findOneBy(array(
                'username' => $username,
            ))
        ;

        $userByEmail = $this->app['orm.em']
            ->getRepository(
                'Application\Entity\UserEntity'
            )
            ->findOneBy(array(
                'email' => $username,
            ))
        ;

        if ($userByUsername) {
            $user = $userByUsername;
        } elseif ($userByEmail) {
            $user = $userByEmail;
        }

        if (! $user && $showExceptionIfNotExistent) {
            throw new UsernameNotFoundException(
                sprintf(
                    'Username or Email "%s" does not exist.',
                    $username
                )
            );
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (! $user instanceof UserEntity) {
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
