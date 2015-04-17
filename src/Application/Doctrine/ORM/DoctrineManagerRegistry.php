<?php

namespace Application\Doctrine\ORM;

use Exception;
use Doctrine\Common\Persistence\ManagerRegistry;

class DoctrineManagerRegistry implements ManagerRegistry
{
    protected $managers;
    protected $connections;
    protected $name;
    public function __construct($name, array $connections, array $managers, $defaultConnection = "default", $defaultManager = "default")
    {
        $this->name = $name;
        $this->managers = $managers;
        $this->connections = $connections;
        $this->defaultManager = $defaultManager;
        $this->defaultConnection = $defaultConnection;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultConnectionName()
    {
        return $this->defaultConnection;
    }

    /**
     * {@inheritDoc}
     */
    public function getConnection($name = null)
    {
        if ($name == null) {
            $name = $this->getDefaultConnectionName();
        }

        return $this->connections[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * {@inheritDoc}
     */
    public function getConnectionNames()
    {
        array_keys($this->connections);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultManagerName()
    {
        return $this->defaultManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getManager($name = null)
    {
        if ($name == null) {
            $name = $this->getDefaultManagerName();
        }

        return $this->managers[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function getManagers()
    {
        return $this->managers;
    }

    /**
     * {@inheritDoc}
     */
    public function resetManager($name = null)
    {
        #@TODO fix it
        throw new Exception("not implemented yet");
    }

    /**
     * {@inheritDoc}
     */
    public function getAliasNamespace($alias)
    {
        #@TODO fix it
        throw new Exception("not implemented yet");
    }

    /**
     * {@inheritDoc}
     */
    public function getManagerNames()
    {
        return array_keys($this->managers);
    }

    /**
     * {@inheritDoc}
     */
    public function getRepository($persistentObject, $persistentManagerName = null)
    {
        $this->getManager($persistentManagerName)->getRepository($persistentObject);
    }

    /**
     * {@inheritDoc}
     */
    public function getManagerForClass($class)
    {
        foreach ($this->managers as $manager) {
            /* @var $manager \Doctrine\ORM\EntityManager */
            if (!$manager->getMetadataFactory()->isTransient($class)) {
                return $manager;
            }
        }

        return;
    }
}
