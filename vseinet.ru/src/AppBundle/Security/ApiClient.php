<?php 

namespace AppBundle\Security;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Http\Message\MessageFactory;
use Http\Client\Common\PluginClient;
use GuzzleHttp\Cookie\CookieJar;
use AppBundle\Session\NativeSessionStorage;


class ApiClient
{
    /**
     * @var string
     */
    protected $apiHost; 

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var NativeSessionStorage
     */
    protected $storage;

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
        SessionInterface $session, 
        NativeSessionStorage $storage, 
        MessageFactory $factory, 
        PluginClient $client
    )
    {
        $this->apiHost = $apiHost;
        $this->session = $session;
        $this->storage = $storage;
        $this->factory = $factory;
        $this->client = $client;
    }

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

    public function getAuth(): array
    {
        $auth = $this->session->get('api.auth');
        if (null === $auth || $auth['expiresAt'] < new \DateTime()) {

            $headers = ['Content-Type' => 'application/json'];

            $request = $this->factory->createRequest('GET', $this->apiHost.'/user/request/', $headers);
            $response = $this->client->sendRequest($request);
            if (Response::HTTP_OK !== $response->getStatusCode()) {
                throw new BadRequestHttpException($response->getReasonPhrase(), null, $response->getStatusCode());
            }

            $cookies = new CookieJar();
            $cookies->extractCookies($request, $response);

            $this->storage->regenerateWithId($cookies->getCookieByName('PHPSESSID')->getValue());

            list('csrfToken' => $csrfToken) = json_decode($response->getBody()->getContents(), true);
            $headers['X-CSRF-TOKEN'] = $csrfToken;

            $credentials = $this->session->get('credentials', []);
            $body = json_encode($credentials + ['clientId' => 1]);

            $request = $cookies->withCookieHeader($this->factory->createRequest('POST', $this->apiHost.'/user/login/', $headers, $body));
            $response = $this->client->sendRequest($request);
            if (Response::HTTP_OK !== $response->getStatusCode()) {
                throw new BadRequestHttpException($response->getReasonPhrase(), null, $response->getStatusCode());
            }
            
            $auth = json_decode($response->getBody()->getContents(), true);
            $auth['expiresAt'] = new \DateTime(sprintf('+%d seconds', $auth['expiresIn'] - 10));
            $auth['csrfToken'] = $csrfToken;

            $this->session->set('api.auth', $auth);
        }

        return $auth;
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

        if (!in_array($response->getStatusCode(), [Response::HTTP_OK, Response::HTTP_CREATED, Response::HTTP_NO_CONTENT])) {
            throw new BadRequestHttpException($response->getReasonPhrase(), null, $response->getStatusCode());
        }

        $content = json_decode($response->getBody()->getContents(), true);

        return $content;
    }
}
