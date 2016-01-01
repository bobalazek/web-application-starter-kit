<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;
use Cocur\Slugify\Slugify;

/**
 * Post Entity
 *
 * @ORM\Table(name="posts")
 * @ORM\Entity(repositoryClass="Application\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class PostEntity
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    protected $content;

    protected $image;

    protected $imageUploadPath;

    protected $imageUploadDir;

    /**
     * @var string
     *
     * @ORM\Column(name="image_url", type="text", nullable=true)
     */
    protected $imageUrl;

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
     * @ORM\ManyToOne(targetEntity="Application\Entity\UserEntity", inversedBy="posts")
     */
    protected $user;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\PostMetaEntity", mappedBy="post", cascade={"all"})
     */
    protected $postMetas;

    /**
     * Helper for metas
     */
    protected $metas;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->postMetas = new ArrayCollection();
    }

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
     * @return PostEntity
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
     * @return PostEntity
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /*** Content ***/
    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return PostEntity
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
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
     * @return PostEntity
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
     * @param string $imageUploadPath
     *
     * @return PostEntity
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
     * @param string $imageUploadDir
     *
     * @return PostEntity
     */
    public function setImageUploadDir($imageUploadDir)
    {
        $this->imageUploadDir = $imageUploadDir;

        return $this;
    }

    /*** Image URL ***/
    /**
     * @return string
     */
    public function getImageUrl()
    {
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

    /*** Image upload ***/
    /**
     * @return PostEntity
     */
    public function imageUpload()
    {
        if (null !== $this->getImage()) {
            $slugify = new Slugify();

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
        
        return $this:
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
     * @return PostEntity
     */
    public function setTimeCreated(\DateTime $timeCreated)
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
     * @return PostEntity
     */
    public function setTimeUpdated(\DateTime $timeUpdated)
    {
        $this->timeUpdated = $timeUpdated;

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
     * @return PostEntity
     */
    public function setUser(UserEntity $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /*** Post Metas ***/
    public function getPostMetas()
    {
        return $this->postMetas;
    }

    public function setPostMetas($postMetas)
    {
        if ($postMetas) {
            foreach ($postMetas as $postMeta) {
                $postMeta->setPost($this);
            }
            $this->postMetas = $postMetas;
        }

        return $this;
    }

    public function addPostMeta(PostMetaEntity $postMeta)
    {
        if (!$this->postMetas->contains($postMeta)) {
            $postMeta->setPost($this);
            $this->postMetas->add($postMeta);
        }

        return $this;
    }
    public function removePostMeta(PostMetaEntity $postMeta)
    {
        $postMeta->setPost(null);
        $this->postMetas->removeElement($postMeta);

        return $this;
    }

    /*** Metas ***/
    public function getMetas($key = null)
    {
        return $key
            ? (isset($this->metas[$key])
                ? $this->metas[$key]
                : null)
            : $this->metas
        ;
    }

    public function setMetas($metas)
    {
        $this->metas = $metas;

        return $this;
    }

    public function hydratePostMetas()
    {
        $postMetas = $this->getPostMetas()->toArray();

        if (count($postMetas)) {
            $metas = array();

            foreach ($postMetas as $postMeta) {
                $metas[$postMeta->getKey()] = $postMeta->getValue();
            }

            $this->setMetas($metas);
        }
    }

    public function convertMetasToPostMetas($uploadPath, $uploadDir)
    {
        $slugify = new Slugify();
        $metas = $this->getMetas();

        if (!empty($metas)) {
            foreach ($metas as $metaKey => $metaValue) {
                $metaEntity = new PostMetaEntity();

                // Check if it's a file!
                if ($metaValue instanceof UploadedFile) {
                    $filename = $slugify->slugify(
                        $metaValue->getClientOriginalName()
                    );
                    $filename .= '_'.sha1(uniqid(mt_rand(), true)).'.'.
                        $metaValue->guessExtension()
                    ;
                    $metaValue->move(
                        $uploadDir,
                        $filename
                    );
                    $metaValue = $uploadPath.$filename;
                }

                $metaEntity
                    ->setKey($metaKey)
                    ->setValue($metaValue)
                ;
                $this
                    ->addPostMeta($metaEntity)
                ;
            }
        }
    }

    /********** Other methods ***********/
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'content' => $this->getContent(),
            'image_url' => $this->getImageUrl(),
            'metas' => $this->getMetas(),
            'time_created' => $this->getTimeCreated()->format(DATE_ATOM),
        );
    }

    /********** Magic Methods **********/
    public function __toString()
    {
        return $this->getTitle();
    }

    /********** Callback Methods **********/
    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->hydratePostMetas();
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
