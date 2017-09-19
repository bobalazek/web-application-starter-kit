<?php

namespace Application\Doctrine\ORM;

use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @author Borut Balazek <bobalazek124@gmail.com>
 */
class DoctrineManagerRegistry implements ManagerRegistry
{
    protected $managers;
    protected $connections;
    protected $defaultManager;
    protected $defaultConnection;
    protected $name;

    public function __construct($name, array $connections, array $managers, $defaultConnection = 'default', $defaultManager = 'default')
    {
        $this->name = $name;
        $this->managers = $managers;
        $this->connections = $connections;
        $this->defaultManager = $defaultManager;
        $this->defaultConnection = $defaultConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultConnectionName()
    {
        return $this->defaultConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection($name = null)
    {
        if ($name === null) {
            $name = $this->getDefaultConnectionName();
        }

        return $this->connections[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionNames()
    {
        array_keys($this->connections);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultManagerName()
    {
        return $this->defaultManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getManager($name = null)
    {
        if ($name === null) {
            $name = $this->getDefaultManagerName();
        }

        return $this->managers[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getManagers()
    {
        return $this->managers;
    }

    /**
     * {@inheritdoc}
     */
    public function resetManager($name = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getAliasNamespace($alias)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getManagerNames()
    {
        return array_keys($this->managers);
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($persistentObject, $persistentManagerName = null)
    {
        $this->getManager($persistentManagerName)->getRepository($persistentObject);
    }

    /**
     * {@inheritdoc}
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
