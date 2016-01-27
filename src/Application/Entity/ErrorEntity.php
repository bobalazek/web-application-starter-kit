<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Error Entity
 *
 * @ORM\Table(name="errors")
 * @ORM\Entity(repositoryClass="Application\Repository\ErrorRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class ErrorEntity extends AbstractImageUpload
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
     * @ORM\Column(name="code", type="integer")
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    protected $message;

    /**
     * @var string
     *
     * @ORM\Column(name="exception", type="text", nullable=true)
     */
    protected $exception;

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
     * @return ErrorEntity
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /*** Code ***/
    /**
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param integer $code
     *
     * @return ErrorEntity
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /*** Message ***/
    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return ErrorEntity
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /*** Exception ***/
    /**
     * @return string
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param string $exception
     *
     * @return ErrorEntity
     */
    public function setException($exception)
    {
        $this->exception = $exception;

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
     * @return ErrorEntity
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
     * @return ErrorEntity
     */
    public function setTimeUpdated(\DateTime $timeUpdated)
    {
        $this->timeUpdated = $timeUpdated;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
            'time_created' => $this->getTimeCreated()->format(DATE_ATOM),
            'time_updated' => $this->getTimeUpdated()->format(DATE_ATOM),
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getMessage();
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
