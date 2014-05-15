<?php
namespace FLM\UberstrapBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    protected static function createClient(array $options = array(), array $server = array())
    {
        if (!isset($options['debug'])) {
            $options['debug'] = false;
        }

        return parent::createClient($options, $server);
    }
}
