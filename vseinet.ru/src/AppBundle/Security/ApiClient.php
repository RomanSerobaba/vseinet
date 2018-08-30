<?php 

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Http\Message\MessageFactory;
use Http\Client\Common\PluginClient;

class ApiClient
{
    /**
     * @var string
     */
    protected $api; 

    /**
     * @var string
     */
    protected $publicId;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var MessageFactory
     */
    protected $factory;

    /**
     * @var PluginClient
     */
    protected $client;


    public function __construct($api, $publicId, $secret, SessionInterface $session, MessageFactory $factory, PluginClient $client)
    {
        $this->api = $api;
        $this->publicId = $publicId;
        $this->secret = $secret;
        $this->session = $session;
        $this->factory = $factory;
        $this->client = $client;
    }

    public function request($method, $uri, array $headers = [], $body = null)
    {
        $token = $this->getAccessToken();
        $headers['Authorization'] = 'Bearer '.$token;

        try {
            return $this->doRequest($method, $uri, $headers, $body);

        } catch (UnauthorizedHttpException $e) {
            $this->session->remove('api.access_token');
            $this->session->remove('api.access_token.expires_at');

            return $this->request($method, $uri, $headers, $body); 
        }
    }

    public function requestWithoutAuth($method, $uri, array $headers = [], $body = null)
    {
        return $this->doRequest($method, $uri, $headers, $body);
    }

    public function get($uri, array $headers = [], $body = null)
    {
        return $this->request('GET', $uri, $headers, $body);
    }

    public function post($uri, array $headers = [], $body = null)
    {
        return $this->request('POST', $uri, $headers, $body);
    }

    protected function doRequest($method, $uri, array $headers = [], $body = null)
    {
        $headers['Content-Type'] = 'application/json';
        if (is_array($body)) {
            $body = json_encode($body);
        }
        $request = $this->factory->createRequest($method, $this->api.$uri, $headers, $body);
        $response = $this->client->sendRequest($request);
        if (Response::HTTP_UNAUTHORIZED === $response->getStatusCode()) {
            throw new UnauthorizedHttpException('');
        }
        if (!in_array($response->getStatusCode(), [Response::HTTP_OK, Response::HTTP_CREATED, Response::HTTP_NO_CONTENT])) {
            throw new BadRequestHttpException($response->getReasonPhrase(), null, $response->getStatusCode());
        }
        $content = json_decode($response->getBody()->getContents(), true);

        return $content;
    }

    protected function getAccessToken()
    {
        $token = $this->session->get('api.access_token');
        $expiresAt = $this->session->get('api.access_token.expires_at');

        if (null === $token || $expiresAt < new \DatetIme()) {
            $content = $this->doRequest('POST', '/authorize/', [], ['publicId' => $this->publicId, 'secret' => $this->secret]);

            $token = $content['accessToken'];
            $expiresAt = new \DateTime(sprintf('+%d seconds', $content['expiresIn'] - 10));

            $this->session->set('api.access_token', $token);
            $this->session->set('api.access_token.expires_at', $expiresAt);
        }

        return $token;
    }
}
