<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Cocur\Slugify\Slugify;

/**
 * Profile Entity
 *
 * @ORM\Table(name="profiles")
 * @ORM\Entity(repositoryClass="Application\Repository\ProfileRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class ProfileEntity
{
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

    /**
     * @var File
     */
    protected $image;

    /**
     * @var string
     */
    protected $imageUploadPath;

    /**
     * @var string
     */
    protected $imageUploadDir;

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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
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
     * @param $title
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
     * @param $firstName
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
     * @param $middleName
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
     * @param $lastName
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
     * @param $gender
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

    /*** Image ***/
    /**
     * @return File
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param File $image
     *
     * @return ProfileEntity
     */
    public function setImage(File $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /*** Image path ***/
    /**
     * @return string
     */
    public function getImageUploadPath()
    {
        return $this->imageUploadPath;
    }

    /**
     * @param $imageUploadPath
     *
     * @return ProfileEntity
     */
    public function setImageUploadPath($imageUploadPath)
    {
        $this->imageUploadPath = $imageUploadPath;

        return $this;
    }

    /*** Image upload dir ***/
    /**
     * @return string
     */
    public function getImageUploadDir()
    {
        return $this->imageUploadDir;
    }

    /**
     * @param $imageUploadDir
     *
     * @return ProfileEntity
     */
    public function setImageUploadDir($imageUploadDir)
    {
        $this->imageUploadDir = $imageUploadDir;

        return $this;
    }

    /*** Image URL ***/
    /**
     * @param boolean $showPlaceholderIfNull
     *
     * @return string
     */
    public function getImageUrl($showPlaceholderIfNull = false)
    {
        if ($showPlaceholderIfNull && $this->imageUrl === null) {
            return $this->getPlaceholderImageUrl();
        }

        return $this->imageUrl;
    }

    /**
     * @param string $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /*** Placeholder Image Src ***/
    /**
     * @return string
     */
    public function getPlaceholderImageUrl()
    {
        return 'http://api.randomuser.me/portraits/lego/'.rand(0, 9).'.jpg';
    }

    /*** Image upload ***/
    /**
     * @return ProfileEntity
     *
     * @throws \Exception If upload dir and path are not set
     */
    public function imageUpload()
    {
        if (null !== $this->getImage()) {
            $uploadDir = $this->getImageUploadDir();
            $uploadPath = $this->getImageUploadPath();
            
            if (!($uploadDir && $uploadPath)) {
                throw new Exception('You must define the image upload dir and path!');
            }
            
            $slugify = new Slugify();

            $filename = $slugify->slugify(
                $this->getImage()->getClientOriginalName()
            );

            $filename .= '_'.sha1(uniqid(mt_rand(), true)).'.'.
                $this->getImage()->guessExtension()
            ;

            $this->getImage()->move(
                $uploadDir,
                $filename
            );

            $this->setImageUrl($uploadPath.$filename);

            $this->setImage(null);
        }
        
        return $this;
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
