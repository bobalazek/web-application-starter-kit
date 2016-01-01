<?php

namespace Application\Entity;

/**
 * Abstract basic entity
 *
 * Some of the basic variables and methods (id, timeCreated and timeUpdated)
 *
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class AbstractBasicEntity
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $timeCreated;

    /**
     * @var \DateTime
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
     * @return AbstractBasicEntity
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @return AbstractBasicEntity
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
     * @return AbstractBasicEntity
     */
    public function setTimeUpdated(\DateTime $timeUpdated)
    {
        $this->timeUpdated = $timeUpdated;

        return $this;
    }

    /**
     * Returns data in array
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'time_created' => $this->getTimeCreated()->format(DATE_ATOM),
            'time_updated' => $this->getTimeUpdated()->format(DATE_ATOM),
        );
    }
}
