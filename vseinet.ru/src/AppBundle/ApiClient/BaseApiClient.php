<?php

namespace AppBundle\ApiClient;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Http\Message\MessageFactory;
use Http\Client\Common\PluginClient;

abstract class BaseApiClient
{
    /**
     * @var string
     */
    protected $apiHost;

    /**
     * @var MessageFactory
     */
    protected $factory;

    /**
     * @var PluginClient
     */
    protected $client;

    public function __construct(
        string $apiHost,
        MessageFactory $factory,
        PluginClient $client
    )
    {
        $this->apiHost = $apiHost;
        $this->factory = $factory;
        $this->client = $client;
    }

    abstract public function getAuth(): array;

    public function request(string $method, $uri, array $headers = [], $body = null)
    {
        $auth = $this->getAuth();
        $headers['Authorization'] = sprintf('Bearer %s', $auth['accessToken']);

        try {
            return $this->doRequest($method, $uri, $headers, $body);
        } catch (UnauthorizedHttpException $e) {
            return $this->request($method, $uri, $headers, $body);
        }
    }

    public function get($uri, array $headers = [], $body = null)
    {
        return $this->request('GET', $uri, $headers, $body);
    }

    public function post($uri, array $headers = [], $body = null)
    {
        return $this->request('POST', $uri, $headers, $body);
    }

    public function put($uri, array $headers = [], $body = null)
    {
        return $this->request('PUT', $uri, $headers, $body);
    }

    public function patch($uri, array $headers = [], $body = null)
    {
        return $this->request('PATCH', $uri, $headers, $body);
    }

    public function delete($uri, array $headers = [], $body = null)
    {
        return $this->request('DELETE', $uri, $headers, $body);
    }

    public function link($uri, array $headers = [], $body = null)
    {
        return $this->request('LINK', $uri, $headers, $body);
    }

    public function unlink($uri, array $headers = [], $body = null)
    {
        return $this->request('UNLINK', $uri, $headers, $body);
    }

    protected function doRequest($method, $uri, array $headers = [], $body = null)
    {
        $headers['Content-Type'] = 'application/json';
        if (is_array($body)) {
            $body = json_encode($body);
        }

        $request = $this->factory->createRequest($method, $this->apiHost.$uri, $headers, $body);
        $response = $this->client->sendRequest($request);
        if (Response::HTTP_UNAUTHORIZED === $response->getStatusCode()) {
            throw new UnauthorizedHttpException();
        }
var_dump(array_column($response->getHeaders(), 'X-Debug-Token-Link', ''), $response->getBody()->getContents());die();
        if (!in_array($response->getStatusCode(), [Response::HTTP_OK, Response::HTTP_CREATED, Response::HTTP_NO_CONTENT])) {
            throw new BadRequestHttpException($response->getReasonPhrase(), null, $response->getStatusCode());
        }

        $content = json_decode($response->getBody()->getContents(), true);

        return $content;
    }
}
