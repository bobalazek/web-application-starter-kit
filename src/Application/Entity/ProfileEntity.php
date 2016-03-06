<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Profile Entity.
 *
 * @ORM\Table(name="profiles")
 * @ORM\Entity(repositoryClass="Application\Repository\ProfileRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class ProfileEntity extends AbstractImageUpload
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
     * Mr., Mrs., Ms., Ing., ...
     *
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=8, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=32, nullable=true)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="middle_name", type="string", length=32, nullable=true)
     */
    protected $middleName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=32, nullable=true)
     */
    protected $lastName;

    /**
     * male or female?
     *
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=8, nullable=true)
     */
    protected $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthdate", type="datetime", nullable=true)
     */
    protected $birthdate;

    /**
     * @var string
     *
     * @ORM\Column(name="image_url", type="text", nullable=true)
     */
    protected $imageUrl;

    /**
     * @ORM\OneToOne(targetEntity="Application\Entity\UserEntity", inversedBy="profile")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

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
     * @return ProfileEntity
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /*** Title ***/
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return ProfileEntity
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /*** Name ***/
    /**
     * @return string
     */
    public function getName()
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    /*** First name ***/
    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return ProfileEntity
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /*** Middle name ***/
    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     *
     * @return ProfileEntity
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /*** Last name ***/
    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return ProfileEntity
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /*** Full name ***/
    /**
     * @return string
     */
    public function getFullName()
    {
        return trim(
            $this->getTitle().' '.
            $this->getFirstName().' '.
            $this->getMiddleName().' '.
            $this->getLastName()
        );
    }

    /*** Gender ***/
    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     *
     * @return ProfileEntity
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /*** Birthdate ***/
    /**
     * @return string
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * @param mixed $birthdate
     *
     * @return ProfileEntity
     */
    public function setBirthdate($birthdate = null)
    {
        if ($birthdate === null) {
            $this->birthdate = null;
        } elseif ($birthdate instanceof \DateTime) {
            $this->birthdate = $birthdate;
        } else {
            $this->birthdate = new \DateTime($birthdate);
        }

        return $this;
    }

    /*** Age ***/
    /**
     * @return string
     */
    public function getAge($format = '%y')
    {
        return $this->getBirthdate()
            ? $this->getBirthdate()->diff(new \DateTime())->format($format)
            : null
        ;
    }

    /*** User ***/
    /**
     * @return UserEntity
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserEntity $user
     *
     * @return ProfileEntity
     */
    public function setUser(UserEntity $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'full_name' => $this->getFullName(),
            'first_name' => $this->getFirstName(),
            'middle_name' => $this->getMiddleName(),
            'last_name' => $this->getLastName(),
            'gender' => $this->getGender(),
            'birthdate' => $this->getBirthdate()
                ? $this->getBirthdate()->format(DATE_ATOM)
                : null,
            'image_url' => $this->getImageUrl(),
        );
    }
}
