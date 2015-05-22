<?php
namespace FLM\UberstrapBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTest;

class WebTestCase extends BaseWebTestCase
{
    protected function setUp()
    {
        $this->loadFixtures(array(
            'FLM\UberstrapBundle\Tests\DataFixtures\TestLoader'
        ));
        $manipulator = $this->getContainer()->get('fos_user.util.user_manipulator');
        $manipulator->create('test', 'test', 'test@example.com', true, false);
        $manipulator->create('admin', 'admin', 'admin@example.com', true, true);

        parent::setUp();
    }
}
