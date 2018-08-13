<?php 

use AppBundle\Container\ContainerAware;

class APIService extends ContainerAware 
{
    public function request($method, $url, $parameters = [])
    {
        $token = $this->getToken();
    }

    public function get($url, $parameters = [])
    {
        return $this->request('GET', $url, $parameters);
    }

    public function post($url, $parameters = [])
    {
        return $this->request('POST', $url, $parameters);
    }

    protected function getToken()
    {
        $token = $this->get('session')->get('bearer_token');
        if (null === $token || $token->isExpired()) {
            $response = $this->request('GET', )

            $request = $this->get('httplug.message_factory')->createRequest('GET', self::GEOCODER_API.$address);
            
        } 

            $response = $this->container->get('httplug.client.guzzle')->sendRequest($request);
    }

    protected function request($method, $url, $parameters = [], $headers = [])
    {
        $request = $this->get('httplug.message_factory')->createRequest('GET', 'https://dev.vseinet.ru/api/v1/'.$url);    
    }
}
