<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use AppBundle\ApiClient\ApiClientException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ExceptionListener
{
    /**
     * @var string
     */
    var $env;

    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(string $env)
    {
        $this->env = $env;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (in_array($this->env, ['dev', 'test'], true)) {
            if ($exception instanceof ApiClientException && !empty($exception->getDebugTokenLink())) {
                $event->setResponse(new RedirectResponse($exception->getDebugTokenLink() . '?panel=exception'));
            }
        }
    }
}