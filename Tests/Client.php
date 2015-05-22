<?php
namespace FLM\UberstrapBundle\Tests;

use Doctrine\ORM\EntityManager;
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

    /**
     * @return EntityManager
     */
    public function getManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
