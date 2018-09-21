<?php 

namespace AppBundle\ApiClient;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Http\Message\MessageFactory;
use Http\Client\Common\PluginClient;

class SiteApiClient extends BaseApiClient
{
    /**
     * @var string
     */
    public $publicId;

    /**
     * @var string
     */
    public $secret;

    /**
     * @var Session
     */
    protected $session;

 
    public function __construct(
        $apiHost, 
        $publicId, 
        $secret, 
        SessionInterface $session, 
        MessageFactory $factory, 
        PluginClient $client
    )
    {
        $this->apiHost = $apiHost;
        $this->publicId = $publicId;
        $this->secret = $secret;
        $this->session = $session;
        $this->factory = $factory;
        $this->client = $client;
    }

    public function getAuth(): array 
    {
        $auth = $this->session->get('site.api.auth');
        if (null === $auth || $auth['expiresAt'] < new \DateTime()) {
            $auth = $this->doRequest('POST', '/authorize/', [], ['publicId' => $this->publicId, 'secret' => $this->secret]); 
            $auth['expiresAt'] = new \DateTime(sprintf('+%d seconds', $auth['expiresIn'] - 10));

            $this->session->set('site.api.auth', $auth);    
        }

        return $auth;
    }
}
