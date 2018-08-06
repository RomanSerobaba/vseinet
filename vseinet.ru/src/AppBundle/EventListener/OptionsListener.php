<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Annotation as VIA;

class OptionsListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ('OPTIONS' === $request->getMethod()) {
            $response = new Response();

            if ($headers = $request->headers->get('Access-Control-Request-Headers')) {
                $response->headers->set('Access-Control-Allow-Headers', $headers);    
            }
            $response->headers->set('Access-Control-Max-Age', 3600);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, PURGE, LINK, UNLINK, OPTIONS');
            $event->setResponse($response);
        }
    }
}