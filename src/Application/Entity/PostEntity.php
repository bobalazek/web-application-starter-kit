<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
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
class PostEntity extends AbstractImageUpload
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\PostMetaEntity", mappedBy="post", cascade={"all"})
     */
    protected $postMetas;

    /**
     * Helper for metas
     *
     * @var array
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
     * @param integer $id
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
    /**
     * @return ArrayCollection
     */
    public function getPostMetas()
    {
        return $this->postMetas->toArray();
    }

    /**
     * @param ArrayCollection $postMetas
     *
     * @return PostEntity
     */
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

    /**
     * @param PostMetaEntity $postMeta
     *
     * @return PostEntity
     */
    public function addPostMeta(PostMetaEntity $postMeta)
    {
        if (!$this->postMetas->contains($postMeta)) {
            $postMeta->setPost($this);
            $this->postMetas->add($postMeta);
        }

        return $this;
    }

    /**
     * @param PostMetaEntity $postMeta
     *
     * @return PostEntity
     */
    public function removePostMeta(PostMetaEntity $postMeta)
    {
        $postMeta->setPost(null);
        $this->postMetas->removeElement($postMeta);

        return $this;
    }

    /*** Metas ***/
    /**
     * @param $key
     *
     * @return mixed
     */
    public function getMetas($key = null)
    {
        return $key
            ? (isset($this->metas[$key])
                ? $this->metas[$key]
                : null)
            : $this->metas
        ;
    }

    /**
     * @return PostEntity
     */
    public function setMetas($metas)
    {
        $this->metas = $metas;

        return $this;
    }

    /**
     * @return void
     */
    public function hydratePostMetas()
    {
        $postMetas = $this->getPostMetas();

        if (count($postMetas)) {
            $metas = array();

            foreach ($postMetas as $postMeta) {
                $metas[$postMeta->getKey()] = $postMeta->getValue();
            }

            $this->setMetas($metas);
        }
    }

    /**
     * @return void
     */
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

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'content' => $this->getContent(),
            'image_url' => $this->getImageUrl(),
            'metas' => $this->getMetas(),
            'time_created' => $this->getTimeCreated()->format(DATE_ATOM),
            'time_updated' => $this->getTimeUpdated()->format(DATE_ATOM),
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

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
