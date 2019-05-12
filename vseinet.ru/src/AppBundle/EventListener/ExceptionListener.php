<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\ApiClient\ApiClientException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Http\Message\MessageFactory;
use Http\Client\Common\PluginClient;

class ExceptionListener
{
    /**
     * @var string
     */
    protected $env;

    /**
     * @var MessageFactory
     */
    protected $factory;

    /**
     * @var PluginClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $channelErrorsUrl;

    public function __construct(string $env, MessageFactory $factory, PluginClient $client, string $channelErrorsUrl)
    {
        $this->env = $env;
        $this->factory = $factory;
        $this->client = $client;
        $this->channelErrorsUrl = $channelErrorsUrl;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($event->isMasterRequest()) {
            $request = $event->getRequest();
            $response = $event->getResponse();

            if ('127.0.0.1' === $request->getClientIp()) {
                return;
            }

            if (Response::HTTP_INTERNAL_SERVER_ERROR === $response->getStatusCode() && $response->headers->has('x-debug-token')) {
                $headers['Content-Type'] = 'application/json';
                $body = json_encode([
                    'text' => $request->getUri()."\n".$response->headers->get('x-debug-token-link'),
                ]);
                $this->client->sendRequest($this->factory->createRequest('POST', $this->channelErrorsUrl, $headers, $body));
            }
        }

        $exception = $event->getException();

        if (in_array($this->env, ['dev', 'test'], true)) {
            if ($exception instanceof ApiClientException && !empty($exception->getDebugTokenLink()) && empty($exception->getParamErrors())) {
                // $event->setResponse(new JsonResponse(json_encode(['errors' => $exception->getParamErrors(),])));
                $event->setResponse(new RedirectResponse($exception->getDebugTokenLink().'?panel=exception'));
            }
        }
    }
}
