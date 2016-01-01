<?php

namespace Application\Entity;

/**
 * Abstract Meta
 *
 * Some default methods for a meta entity (id, key and value)
 *
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class AbstractMeta extends AbstractBasicEntity
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $value;

    /*** Key ***/
    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return AbstractMeta
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /*** Value ***/
    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return AbstractMeta
     */
    public function setValue($value)
    {
        $this->value = $value;

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
            'key' => $this->getKey(),
            'value' => $this->getValue(),
            'time_created' => $this->getTimeCreated()->format(DATE_ATOM),
            'time_updated' => $this->getTimeUpdated()->format(DATE_ATOM),
        );
    }

    /**
     * Converts the key and value to string
     *
     * @return string
     */
    public function __toString()
    {
        $data = array();
        $key = $this->getKey();
        $value = $this->getValue();

        // Prevent double encoding
        if ($value[0] == '{' || $value[0] == '[') {
            $value = json_decode($value);
        }

        $data[$key] = $value;

        return json_encode($data);
    }
}
