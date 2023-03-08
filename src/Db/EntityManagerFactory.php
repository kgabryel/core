<?php

namespace Frankie\Core\Db;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Ds\Map;

final class EntityManagerFactory
{
    private Map $config;

    private EntityManager $entityManager;

    private bool $devMode;

    private string $path;

    public function __construct(ConfigGetter $configGetter, string $path)
    {
        $this->path = $path;
        $this->config = $configGetter->get();
        $this->devMode = $configGetter->isDevMode();
    }

    public function get(): EntityManager
    {
        return $this->entityManager;
    }

    public function build(): self
    {
        $config = ORMSetup::createAttributeMetadataConfiguration([$this->path], $this->devMode);
        $connection = DriverManager::getConnection($this->config->toArray(), $config);
        $this->entityManager = new EntityManager($connection, $config);

        return $this;
    }
}
