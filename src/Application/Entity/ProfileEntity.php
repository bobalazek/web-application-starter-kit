<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Profile Entity
 *
 * @ORM\Table(name="profiles")
 * @ORM\Entity(repositoryClass="Application\Repository\ProfileRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ProfileEntity
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

    protected $image;

    protected $imageUploadPath;

    protected $imageUploadDir;

    /**
     * @var string
     *
     * @ORM\Column(name="image_url", type="text", nullable=true)
     */
    protected $imageUrl;

    /***** Relationship Variables *****/
    /**
     * @ORM\OneToOne(targetEntity="Application\Entity\UserEntity", inversedBy="profile")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /*************** Methods ***************/
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

    /*** Title ***/
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /*** Name ***/
    public function getName()
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    /*** First name ***/
    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /*** Middle name ***/
    public function getMiddleName()
    {
        return $this->middleName;
    }

    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /*** Last name ***/
    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /*** Full name ***/
    public function getFullName()
    {
        return $this->getTitle().' '.
            $this->getFirstName().' '.
            $this->getMiddleName().' '.
            $this->getLastName();
    }

    /*** Gender ***/
    public function getGender()
    {
        return $this->gender;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /*** Birthdate ***/
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    public function setBirthdate($birthdate = null)
    {
        if ($birthdate == null) {
            $this->birthdate = null;
        } elseif ($birthdate instanceof \DateTime) {
            $this->birthdate = $birthdate;
        } else {
            $this->birthdate = new \DateTime($birthdate);
        }

        return $this;
    }

    /*** Age ***/
    public function getAge($format = '%y')
    {
        return $this
            ->getBirthdate()
            ->diff(new \DateTime())
            ->format($format)
        ;
    }

    /*** Image ***/
    public function getImage()
    {
        return $this->image;
    }

    public function setImage(\Symfony\Component\HttpFoundation\File\File $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /*** Image path ***/
    public function getImageUploadPath()
    {
        return $this->imageUploadPath;
    }

    public function setImageUploadPath($imageUploadPath)
    {
        $this->imageUploadPath = $imageUploadPath;

        return $this;
    }

    /*** Image upload dir ***/
    public function getImageUploadDir()
    {
        return $this->imageUploadDir;
    }

    public function setImageUploadDir($imageUploadDir)
    {
        $this->imageUploadDir = $imageUploadDir;

        return $this;
    }

    /*** Image URL ***/
    public function getImageUrl($showPlaceholderIfNull = false)
    {
        if ($showPlaceholderIfNull && $this->imageUrl == null) {
            return $this->getPlaceholderImageUrl();
        }

        return $this->imageUrl;
    }

    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /*** Placeholder Image Src ***/
    public function getPlaceholderImageUrl()
    {
        return 'http://api.randomuser.me/portraits/lego/'.rand(0, 9).'.jpg';
    }

    /*** Image upload ***/
    public function imageUpload()
    {
        if (null === $this->getImage()) {
            return;
        }

        $slugify = new \Cocur\Slugify\Slugify();

        $filename = $slugify->slugify(
            $this->getImage()->getClientOriginalName()
        );

        $filename .= '_'.sha1(uniqid(mt_rand(), true)).'.'.
            $this->getImage()->guessExtension()
        ;

        $this->getImage()->move(
            $this->getImageUploadDir(),
            $filename
        );

        $this->setImageUrl($this->getImageUploadPath().$filename);

        $this->setImage(null);
    }

    /*** User ***/
    public function setUser(\Application\Entity\UserEntity $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    /********** Other Methods **********/
    public function toArray($includeAllData = false)
    {
        $data = array();

        $data['id'] = $this->getId();
        $data['title'] = $this->getTitle();
        $data['firstName'] = $this->getFirstName();
        $data['middleName'] = $this->getMiddleName();
        $data['lastName'] = $this->getLastName();
        $data['gender'] = $this->getGender();
        $data['birthdate'] = $this->getBirthdate();
        $data['imageUrl'] = $this->getImageUrl();

        return $data;
    }
}
