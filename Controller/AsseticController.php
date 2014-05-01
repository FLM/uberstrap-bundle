<?php
namespace FLM\UberstrapBundle\Controller;

use Symfony\Bundle\AsseticBundle\Controller\AsseticController as BaseAsseticController;

/**
 * Cache all assets served with the assetic controller (should only affect dev env)
 */
class AsseticController extends BaseAsseticController
{
    public function render($name, $pos = null)
    {
        $response = parent::render($name, $pos);

        $response->setPublic();
        $dt = new \DateTime();
        $dt->modify('+1 year');
        $response->setExpires($dt);
        $response->setMaxAge(26551332);

        return $response;
    }
}
