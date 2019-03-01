<?php

namespace AdminBundle\EventListener;

use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ProfilerListener
{
    protected $profiler;

    public function __construct(Profiler $profiler)
    {
        $this->profiler = $profiler;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $session = $event->getRequest()->getSession();

        if (!$session->get('profiler.enabled')) {
            $this->profiler->disable();
        }
    }
}
