<?php
namespace FLM\UberstrapBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @DI\Service
 */
class TemplateListener
{
    /**
     * @DI\Inject("%kernel.environment%")
     */
    public $environment;

    /**
     * @DI\Observe("kernel.view")
     * @param GetResponseForControllerResultEvent $event A GetResponseForControllerResultEvent instance
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        /**
         * Disable streamable templates in dev environment, since life sucks without the debug toolbar.
         * Don't stream subrequests since that breaks template inheritance for some reason.
         */
        $streamable = ($this->environment !== 'dev' && $event->getRequestType() === Kernel::MASTER_REQUEST);
        if ($request->attributes->has('_template_streamable')) {
            $request->attributes->set('_template_streamable', $streamable);
        }
    }
}
