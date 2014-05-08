<?php
namespace FLM\UberstrapBundle\Tests;

use Doctrine\ORM\Tools\SchemaTool;
use Hautelook\AliceBundle\Alice\DataFixtureLoader;
use Symfony\Bundle\FrameworkBundle\Client as BaseClient;
use FLM\UberstrapBundle\Tests\DataFixtures\TestLoader;
use Doctrine\DBAL\Connection;

class Client extends BaseClient
{
    /**
     * @var Connection
     */
    protected static $connection;

    protected $requested;
    protected $loader;
    protected $fixtures;

    /**
     * Log in this client as the "test" user using HTTP Authentication
     */
    public function setServerParameters(array $server)
    {
        $server = array_merge(
            $server,
            array(
                'PHP_AUTH_USER' => 'test',
                'PHP_AUTH_PW'   => 'test',
            )
        );

        if (isset($server['fixtures'])) {
            $this->fixtures = $server['fixtures'];
        }

        parent::setServerParameters($server);
    }

    protected function doRequest($request)
    {
        if ($this->requested) {
            $this->kernel->shutdown();
            $this->kernel->boot();
        }

        $this->injectConnection();
        $this->requested = true;

        return $this->kernel->handle($request);
    }

    /**
     * @param array $fixtures
     * @return TestLoader
     */
    protected function createLoader(array $fixtures = null)
    {
        return new TestLoader($fixtures);
    }

    protected function injectConnection()
    {
        if (null === self::$connection) {
            self::$connection = $this->getContainer()->get('doctrine.dbal.default_connection');
            $this->loader = $this->createLoader($this->fixtures);
            $this->loader->setContainer($this->getContainer());
            $this->generateSchema();
        } else {
            if (!$this->requested && self::$connection->isTransactionActive()) {
                self::$connection->rollback();
            }
            $this->getContainer()->set('doctrine.dbal.default_connection', self::$connection);
        }

        if (! $this->requested) {
            self::$connection->beginTransaction();
        }
    }

    public function getManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return null
     */
    protected function generateSchema()
    {
        $metadatas = $this->getMetadatas();

        if (!empty($metadatas)) {
            $tool = new SchemaTool($this->getManager());
            $tool->dropSchema($metadatas);
            $tool->createSchema($metadatas);
            $this->loadFixtures($this->loader);
        }
    }

    /**
     * @param DataFixtureLoader $loader
     */
    protected function loadFixtures(DataFixtureLoader $loader)
    {
        $loader->load($this->getManager());
    }

    /**
     * @return array
     */
    protected function getMetadatas()
    {
        return $this->getManager()->getMetadataFactory()->getAllMetadata();
    }
}
