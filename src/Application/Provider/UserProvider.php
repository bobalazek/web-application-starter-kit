<?php

namespace Application\Provider;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Application\Entity\UserEntity;
use Silex\Application;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class UserProvider implements UserProviderInterface
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
     * @throws UsernameNotFoundException If user was not found
     */
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

        if (!$user && $showExceptionIfNotExistent) {
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
