<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User Entity
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Application\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserEntity
    implements AdvancedUserInterface, \Serializable
{
    /*************** Variables ***************/
    /********** General Variables **********/
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * What is the locale for this user?
     *
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=8, nullable=true)
     */
    protected $locale = 'en_US';

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=64, unique=true)
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=64, unique=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    protected $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     */
    protected $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    protected $token;

    /**
     * @var string
     *
     * @ORM\Column(name="access_token", type="string", length=255, nullable=true)
     */
    protected $accessToken;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean")
     */
    protected $locked = false;

    /**
     * @var string
     *
     * @ORM\Column(name="reset_password_code", type="string", length=255, nullable=true, unique=true)
     */
    protected $resetPasswordCode;

    /**
     * @var string
     *
     * @ORM\Column(name="activation_code", type="string", length=255, nullable=true, unique=true)
     */
    protected $activationCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_last_active", type="datetime", nullable=true)
     */
    protected $timeLastActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_created", type="datetime")
     */
    protected $timeCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_updated", type="datetime")
     */
    protected $timeUpdated;

    /***** Relationship Variables *****/
    /**
     * @ORM\OneToOne(targetEntity="Application\Entity\ProfileEntity", mappedBy="user", cascade={"all"})
     **/
    protected $profile;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Application\Entity\RoleEntity", inversedBy="users")
     * @ORM\JoinTable(
     *      name="user_roles",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *  )
     */
    protected $roles;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\PostEntity", mappedBy="user", cascade={"all"})
     */
    protected $posts;

    /***** Other Variables *****/
    protected $expired = false; // userExpired / accountExpired
    protected $credentialsExpired = false;

    /*************** Methods ***************/
    /***** Constructor *****/
    public function __construct()
    {
        $this->setSalt(
            md5(uniqid(null, true))
        );

        $this->setToken(
            md5(uniqid(null, true))
        );

        $this->setAccessToken(
            md5(uniqid(null, true))
        );

        $this->setActivationCode(
            md5(uniqid(null, true))
        );

        $this->setResetPasswordCode(
            md5(uniqid(null, true))
        );
    }

    /***** Getters, Setters and Other stuff *****/
    /*** Id ***/
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /*** Locale ***/
    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /*** Username ***/
    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /*** Email ***/
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /*** Password ***/
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        if ($password) {
            $this->password = $password;
        }

        return $this;
    }

    /*** Plain password ***/
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword, \Symfony\Component\Security\Core\Encoder\EncoderFactory $encoderFactory = null)
    {
        $this->plainPassword = $plainPassword;

        if ($encoderFactory) {
            $encoder = $encoderFactory->getEncoder($this);

            $password = $encoder->encodePassword(
                $this->getPlainPassword(),
                $this->getSalt()
            );

            $this->setPassword($password);
        }

        return $this;
    }

    /*** Salt ***/
    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /*** Token ***/
    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /*** Access Token ***/
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /*** Enabled ***/
    public function getEnabled()
    {
        return $this->enabled;
    }

    public function isEnabled()
    {
        return $this->getEnabled();
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function enable()
    {
        $this->setEnabled(true);

        return $this;
    }

    public function disable()
    {
        $this->setEnabled(false);

        return $this;
    }

    /*** Locked ***/
    public function getLocked()
    {
        return $this->locked;
    }

    public function isLocked()
    {
        return $this->getLocked();
    }

    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    public function lock($reason = '')
    {
        $this->setLocked(true);

        return $this;
    }

    public function isAccountNonLocked()
    {
        return ! $this->isLocked();
    }

    /*** Reset password code ***/
    public function getResetPasswordCode()
    {
        return $this->resetPasswordCode;
    }

    public function setResetPasswordCode($resetPasswordCode)
    {
        $this->resetPasswordCode = $resetPasswordCode;

        return $this;
    }

    /*** Activate account code ***/
    public function getActivationCode()
    {
        return $this->activationCode;
    }

    public function setActivationCode($activationCode)
    {
        $this->activationCode = $activationCode;

        return $this;
    }

    /*** Time last active ***/
    public function getTimeLastActive()
    {
        return $this->timeLastActive;
    }

    public function setTimeLastActive(\DateTime $timeLastActive = null)
    {
        $this->timeLastActive = $timeLastActive;

        return $this;
    }

    /*** Time created ***/
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    public function setTimeCreated(\DateTime $timeCreated)
    {
        $this->timeCreated = $timeCreated;

        return $this;
    }

    /*** Time updated ***/
    public function getTimeUpdated()
    {
        return $this->timeUpdated;
    }

    public function setTimeUpdated(\DateTime $timeUpdated)
    {
        $this->timeUpdated = $timeUpdated;

        return $this;
    }

    /*** Expired ***/
    public function getExpired()
    {
        return $this->expired;
    }

    public function isExpired()
    {
        return $this->getExpired();
    }

    public function isAccountNonExpired()
    {
        return ! $this->getExpired();
    }

    /*** Credentials expired ***/
    public function getCredentialsExpired()
    {
        return $this->credentialsExpired;
    }

    public function isCredentialsExpired()
    {
        return $this->getCredentialsExpired();
    }

    public function isCredentialsNonExpired()
    {
        return ! $this->getExpired();
    }

   /*** Roles ***/
    public function getRoles()
    {
        $rolesArray = array();

        $userRoles = $this->roles;

        if ($userRoles) {
            $rolesArray = $userRoles->toArray();
        }

        if ($rolesArray) {
            $rolesArray[] = 'ROLE_USER';
            $rolesArray = array_unique($rolesArray);

            return $rolesArray;
        } elseif ($this->getId() == 1) {
            // The user with ID 1 ist normally the super admin
            return array('ROLE_SUPER_ADMIN');
        }

        return array('ROLE_USER'); // Fallback, when no roles are found
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRole(\Application\Entity\RoleEntity $role = null)
    {
        return $this->roles->contains($role);
    }

    public function addRole(\Application\Entity\RoleEntity $role = null)
    {
        if (! $this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRole($role = null)
    {
        if (is_string($role)) {
            foreach ($this->roles as $singleRole) {
                if ($singleRole->getRole() == $role) {
                    $this->roles->removeElement($singleRole);
                }
            }
        } else {
            if ($this->roles->contains($role)) {
                $this->roles->removeElement($role);
            }
        }

        return $this;
    }

    /*** Profile ***/
    public function getProfile()
    {
        return $this->profile;
    }

    public function setProfile(\Application\Entity\ProfileEntity $profile)
    {
        $this->profile = $profile;

        $this->getProfile()->setUser($this);

        return $this;
    }

    /*** Posts ***/
    public function getPosts()
    {
        return $this->posts;
    }

    public function setPosts($posts)
    {
        $this->posts = $posts;

        return $this;
    }

    /***** Other AdvancedUserInterface Methods *****/
    public function isEqualTo(AdvancedUserInterface $user)
    {
        if (! $user instanceof AdvancedUserInterface) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    public function eraseCredentials()
    {
        $this->setPlainPassword(null);
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->salt,
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->salt,
        ) = unserialize($serialized);
    }

    /********** Magic Methods **********/
    public function __toString()
    {
        return $this->getUsername()
            ? $this->getUsername()
            : ''
        ;
    }

    /********** Other Methods **********/
    public function toArray($includeAllData = false)
    {
        $data = array();

        $data['id'] = $this->getId();
        $data['locale'] = $this->getLocale();
        $data['username'] = $this->getUsername();
        $data['email'] = $this->getEmail();
        $data['token'] = $this->getToken();

        if ($includeAllData) {
            $data['token'] = $this->getToken();
            $data['accessToken'] = $this->getAccessToken();
            $data['salt'] = $this->getSalt();
            $data['password'] = $this->getPassword();
            $data['enabled'] = $this->isEnabled();
            $data['locked'] = $this->isLocked();
            $data['resetPasswordCode'] = $this->getResetPasswordCode();
            $data['activationCode'] = $this->getActivationCode();
        }

        return $data;
    }

    public function toJson()
    {
        return array(
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'first_name' => $this->getProfile()->getFirstName(),
            'last_name' => $this->getProfile()->getLastName(),
            'full_name' => $this->getProfile()->getFullName(),
            'enabled' => $this->getEnabled(),
            'locked' => $this->getLocked(),
            'time_created' => $this->getTimeCreated(),
            'access_token' => $this->getAccessToken(),
        );
    }

    /********** Callback Methods **********/
    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setTimeUpdated(new \DateTime('now'));
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setTimeUpdated(new \DateTime('now'));
        $this->setTimeCreated(new \DateTime('now'));
    }
}
