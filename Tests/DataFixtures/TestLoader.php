<?php
namespace FLM\UberstrapBundle\Tests\DataFixtures;

use Hautelook\AliceBundle\Alice\DataFixtureLoader;
use Nelmio\Alice\Fixtures;

class TestLoader extends DataFixtureLoader
{
    protected $fixtures;

    function __construct(array $fixtures = null)
    {
        $this->fixtures = $fixtures;
    }

    /**
     * {@inheritDoc}
     */
    protected function getFixtures()
    {
        $fixtures = $this->container->getParameter('flm_uberstrap.fixtures');
        if (!is_array($fixtures)) {
            $fixtures = array($fixtures);
        }

        $files = array();
        foreach ($fixtures as $fixture) {
            if (file_exists($fixture)) {
                $files[] = $fixture;
            }
        }

        if ($this->fixtures !== null) {
            foreach ($this->fixtures as $fixture) {
                if (file_exists($fixture)) {
                    $files[] = $fixture;
                }
            }
        }

        return $files;
    }
}
