<?php

namespace AppBundle\ApiClient;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Http\Message\MessageFactory;
use Http\Client\Common\PluginClient;
use GuzzleHttp\Cookie\CookieJar;
use AppBundle\Session\NativeSessionStorage;

class UserApiClient extends BaseApiClient
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var NativeSessionStorage
     */
    protected $storage;


    public function __construct(
        string $apiHost,
        SessionInterface $session,
        NativeSessionStorage $storage,
        MessageFactory $factory,
        PluginClient $client,
        string $env
    )
    {
        $this->apiHost = $apiHost;
        $this->session = $session;
        $this->storage = $storage;
        $this->factory = $factory;
        $this->client = $client;
        $this->env = $env;
    }

    public function getAuth(): array
    {
        $auth = $this->session->get('user.api.auth');
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

            $this->session->set('user.api.auth', $auth);
        }

        return $auth;
    }
}
