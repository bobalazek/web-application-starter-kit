<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Post Meta Entity
 *
 * @ORM\Table(name="post_metas")
 * @ORM\Entity(repositoryClass="Application\Repository\PostMetaRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class PostMetaEntity extends AbstractMeta
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
     * @ORM\Column(name="`key`", type="string", length=255)
     */
    protected $key;

    /**
     * @var string
     *
     * @ORM\Column(name="`value`", type="text")
     */
    protected $value;

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
     * @ORM\ManyToOne(targetEntity="Application\Entity\PostEntity", inversedBy="postMetas")
     */
    protected $post;

    /*** Post ***/
    /**
     * @return PostEntity
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param PostEntity $post
     */
    public function setPost(PostEntity $post = null)
    {
        $this->post = $post;

        return $this;
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
