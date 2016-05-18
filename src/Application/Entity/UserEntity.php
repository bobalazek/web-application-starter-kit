<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

/**
 * User Entity.
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Application\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class UserEntity implements AdvancedUserInterface, \Serializable
{
    /**
     * @var int
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
     * We must confirm the new password, so temporary save it inside this field.
     *
     * @var string
     *
     * @ORM\Column(name="new_email", type="string", length=64)
     */
    protected $newEmail;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="json_array", nullable=true)
     */
    protected $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * Used only when saving the user.
     *
     * @var string
     */
    protected $plainPassword;

    /**
     * Used only when saving a new password.
     *
     * @var string
     */
    protected $oldPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     */
    protected $salt;

    /**
     * Used for emails & co.
     *
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    protected $token;

    /**
     * Used for authentication & co.
     *
     * @var string
     *
     * @ORM\Column(name="access_token", type="string", length=255, nullable=true)
     */
    protected $accessToken;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled = true;

    /**
     * @var bool
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
     * @var string
     *
     * @ORM\Column(name="new_email_code", type="string", length=255, nullable=true, unique=true)
     */
    protected $newEmailCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_last_active", type="datetime", nullable=true)
     */
    protected $timeLastActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_reset_password_code_expires", type="datetime", nullable=true)
     */
    protected $timeResetPasswordCodeExpires;

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

    /**
     * @ORM\OneToOne(targetEntity="Application\Entity\ProfileEntity", mappedBy="user", cascade={"all"})
     **/
    protected $profile;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\PostEntity", mappedBy="user", cascade={"all"})
     */
    protected $posts;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\UserActionEntity", mappedBy="user", cascade={"all"})
     */
    protected $userActions;

    /**
     * Otherwise known as: userExpired / accountExpired.
     *
     * @var bool
     */
    protected $expired = false;

    /**
     * @var bool
     */
    protected $credentialsExpired = false;

    /**
     * The constructor.
     */
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

        $this->setNewEmailCode(
            md5(uniqid(null, true))
        );

        $this->posts = new ArrayCollection();
        $this->userActions = new ArrayCollection();
    }

    /*** Id ***/
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return UserEntity
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /*** Locale ***/
    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return UserEntity
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /*** Username ***/
    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return UserEntity
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /*** Email ***/
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return UserEntity
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /*** New email ***/
    /**
     * @return string
     */
    public function getNewEmail()
    {
        return $this->newEmail;
    }

    /**
     * @param string $newEmail
     *
     * @return UserEntity
     */
    public function setNewEmail($newEmail)
    {
        $this->newEmail = $newEmail;

        return $this;
    }

    /*** Password ***/
    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return UserEntity
     */
    public function setPassword($password)
    {
        if ($password) {
            $this->password = $password;
        }

        return $this;
    }

    /*** Plain password ***/
    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string         $plainPassword
     * @param EncoderFactory $encoderFactory
     *
     * @return UserEntity
     */
    public function setPlainPassword($plainPassword, EncoderFactory $encoderFactory = null)
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

    /*** Old password ***/
    /**
     * @return string
     */
    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    /**
     * @param string $oldPassword
     *
     * @return UserEntity
     */
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    /*** Salt ***/
    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     *
     * @return UserEntity
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /*** Token ***/
    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return UserEntity
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /*** Access Token ***/
    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     *
     * @return UserEntity
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /*** Enabled ***/
    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getEnabled();
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return UserEntity
     */
    public function enable()
    {
        $this->setEnabled(true);

        return $this;
    }

    /**
     * @return UserEntity
     */
    public function disable()
    {
        $this->setEnabled(false);

        return $this;
    }

    /*** Locked ***/
    /**
     * @return bool
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->getLocked();
    }

    /**
     * @param bool $locked
     *
     * @return UserEntity
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * @return UserEntity
     */
    public function lock()
    {
        $this->setLocked(true);

        return $this;
    }

    /**
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return !$this->isLocked();
    }

    /*** Reset password code ***/
    /**
     * @return string
     */
    public function getResetPasswordCode()
    {
        return $this->resetPasswordCode;
    }

    /**
     * @param string $resetPasswordCode
     *
     * @return UserEntity
     */
    public function setResetPasswordCode($resetPasswordCode)
    {
        $this->resetPasswordCode = $resetPasswordCode;

        return $this;
    }

    /*** Activation code ***/
    /**
     * @return string
     */
    public function getActivationCode()
    {
        return $this->activationCode;
    }

    /**
     * @param string $activationCode
     *
     * @return UserEntity
     */
    public function setActivationCode($activationCode)
    {
        $this->activationCode = $activationCode;

        return $this;
    }

    /*** New email code ***/
    /**
     * @return string
     */
    public function getNewEmailCode()
    {
        return $this->newEmailCode;
    }

    /**
     * @param string $newEmailCode
     *
     * @return UserEntity
     */
    public function setNewEmailCode($newEmailCode)
    {
        $this->newEmailCode = $newEmailCode;

        return $this;
    }

    /*** Time last active ***/
    /**
     * @return \DateTime
     */
    public function getTimeLastActive()
    {
        return $this->timeLastActive;
    }

    /**
     * @param $timeLastActive
     *
     * @return UserEntity
     */
    public function setTimeLastActive(\Datetime $timeLastActive = null)
    {
        $this->timeLastActive = $timeLastActive;

        return $this;
    }

    /*** Time reset password code expires ***/
    /**
     * @return \DateTime
     */
    public function getTimeResetPasswordCodeExpires()
    {
        return $this->timeResetPasswordCodeExpires;
    }

    /**
     * @param $timeResetPasswordCodeExpires
     *
     * @return UserEntity
     */
    public function setTimeResetPasswordCodeExpires(\Datetime $timeResetPasswordCodeExpires = null)
    {
        $this->timeResetPasswordCodeExpires = $timeResetPasswordCodeExpires;

        return $this;
    }

    /*** Time created ***/
    /**
     * @return \DateTime
     */
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    /**
     * @param \DateTime $timeCreated
     *
     * @return UserEntity
     */
    public function setTimeCreated(\Datetime $timeCreated)
    {
        $this->timeCreated = $timeCreated;

        return $this;
    }

    /*** Time updated ***/
    /**
     * @return \DateTime
     */
    public function getTimeUpdated()
    {
        return $this->timeUpdated;
    }

    /**
     * @param \DateTime $timeUpdated
     *
     * @return UserEntity
     */
    public function setTimeUpdated(\DateTime $timeUpdated)
    {
        $this->timeUpdated = $timeUpdated;

        return $this;
    }

    /*** Expired ***/
    /**
     * @return bool
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        return $this->getExpired();
    }

    /**
     * @return bool
     */
    public function isAccountNonExpired()
    {
        return !$this->getExpired();
    }

    /*** Credentials expired ***/
    /**
     * @return bool
     */
    public function getCredentialsExpired()
    {
        return $this->credentialsExpired;
    }

    /**
     * @return bool
     */
    public function isCredentialsExpired()
    {
        return $this->getCredentialsExpired();
    }

    /**
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        return !$this->getExpired();
    }

    /*** Roles ***/
    /**
     * @return array
     */
    public function getRoles()
    {
        $roles = is_array($this->roles)
            ? $this->roles
            : array()
        ;
        $roles[] = 'ROLE_USER';

        return (array) array_unique($roles, SORT_REGULAR);
    }

    /**
     * @param array $roles
     *
     * @return UserEntity
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array(
            $role,
            $this->getRoles()
        );
    }

    /*** Profile ***/
    /**
     * @return ProfileEntity
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param ProfileEntity $profile
     *
     * @return UserEntity
     */
    public function setProfile(ProfileEntity $profile)
    {
        $this->profile = $profile;

        $this->getProfile()->setUser($this);

        return $this;
    }

    /*** Posts ***/
    /**
     * @return array
     */
    public function getPosts()
    {
        return $this->posts->toArray();
    }

    /**
     * @param ArrayCollection $posts
     *
     * @return UserEntity
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;

        return $this;
    }

    /*** User actions ***/
    /**
     * @return array
     */
    public function getUserActions()
    {
        return $this->userActions->toArray();
    }

    /**
     * @param $userActions
     *
     * @return UserEntity
     */
    public function setUserActions($userActions)
    {
        $this->userActions = $userActions;

        return $this;
    }

    /**
     * @param AdvancedUserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(AdvancedUserInterface $user)
    {
        if (!($user instanceof AdvancedUserInterface)) {
            return false;
        }

        if ($this->getPassword() !== $user->getPassword()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->getUsername() !== $user->getUsername()) {
            return false;
        }

        if (serialize($this->getRoles()) !== serialize($user->getRoles())) {
            return false;
        }

        return true;
    }

    /**
     */
    public function eraseCredentials()
    {
        $this->setPlainPassword(null);
    }

    /**
     * @return string
     */
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

    /**
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->salt) = unserialize($serialized);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUsername();
    }

    /**
     * @param $allData Show all the data for this user.
     *
     * @return array
     */
    public function toArray($allData = true)
    {
        $data = array(
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'title' => $this->getProfile()->getTitle(),
            'first_name' => $this->getProfile()->getFirstName(),
            'middle_name' => $this->getProfile()->getMiddleName(),
            'last_name' => $this->getProfile()->getLastName(),
            'full_name' => $this->getProfile()->getFullName(),
            'gender' => $this->getProfile()->getGender(),
            'birthdate' => $this->getProfile()->getBirthdate()->format('Y-m-d'),
            'image_url' => $this->getProfile()->getImageUrl(),
        );

        if ($allData) {
            $data = array_merge($data, array(
                'id' => $this->getId(),
                'time_created' => $this->getTimeCreated()->format(DATE_ATOM),
            ));
        }

        return $data;
    }

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
