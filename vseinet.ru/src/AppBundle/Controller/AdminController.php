<?php 

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Annotation as VIA;
use GuzzleHttp\Cookie\CookieJar;

/**
 * @Security("is_granted('ROLE_EMPLOYEE')") 
 */
class AdminController extends Controller
{
    const API = 'https://dev.vseinet.ru';

    /**
     * @VIA\Get(
     *     name="admin",
     *     path="/admin/"
     * )
     */
    public function adminAction()
    {
        return new Response('Welcome, '.$this->getUser()->person->getFirstName().', to admin page!');
    }

    /**
     * @VIA\Get(
     *     name="authority",
     *     path="/authority/"
     * )
     */
    public function authorityActyion(Request $request)
    {
        $targetUrl = $request->query->get('targetUrl');
        if (empty($targetUrl)) {
            $targetUrl = $this->generateUrl('admin');
        }

        $auth = $this->getAuth();
        if (null !== $auth) {
            return $this->redirect($targetUrl);
        }

        $factory = $this->container->get('httplug.message_factory');
        $client = $this->container->get('httplug.client.guzzle');

        $headers = ['Content-Type' => 'application/json'];

        $apiRequest = $factory->createRequest('GET', self::API.'/user/request/', $headers);
        $apiResponse = $client->sendRequest($apiRequest);
        if (200 !== $apiResponse->getStatusCode()) {
            throw new BadRequestHttpException();
        }

        $cookies = new CookieJar();
        $cookies->extractCookies($apiRequest, $apiResponse);

        $this->get('session.storage.native')->regenerateWithId($cookies->getCookieByName('PHPSESSID')->getValue());

        $body = json_decode($apiResponse->getBody()->getContents(), true);
        $csrfToken = $body['csrfToken'];
        $headers['X-CSRF-TOKEN'] = $csrfToken;

        $credentials = $this->get('session')->get('credentials', []);
        $body = json_encode($credentials + ['clientId' => 1]);

        $apiRequest = $cookies->withCookieHeader($factory->createRequest('POST', self::API.'/user/login/', $headers, $body));
        $apiResponse = $client->sendRequest($apiRequest);
        if (200 !== $apiResponse->getStatusCode()) {
            throw new BadRequestHttpException();
        }
        
        $data = json_decode($apiResponse->getBody()->getContents(), true);
        $data['expiresAt'] = new \DateTime(sprintf('+%d seconds', $data['expiresIn'] - 10));

        $this->get('session')->set('api_authority_data', $data);        

        return $this->render('Admin/authority.html.twig', $data + ['targetUrl' => $targetUrl, 'csrfToken' => $csrfToken]);
    }

    /**
     * @VIA\Get(
     *     name="clockin",
     *     path="/clockin/"
     * )
     */
    public function clockInAction(Request $request)
    {
        $user = $this->getUser();
        $auth = $this->getAuth();
        if (null === $auth) {
            return $this->redirectToRoute('authority', [
                'targetUrl' => $this->generateUrl('clockin'),
            ]);
        }

        $factory = $this->container->get('httplug.message_factory');
        $client = $this->container->get('httplug.client.guzzle');

        $url = sprintf('%s/api/v1/work/%s/', self::API, null === $user->clockInTime ? 'start' : 'stop');
        $headers['Authorization'] = 'Bearer '.$auth['accessToken'];

        $apiRequest = $factory->createRequest('PUT', $url, $headers);
        $apiResponse = $client->sendRequest($apiRequest);
        if (204 !== $apiResponse->getStatusCode()) {
            throw new BadRequestHttpException();
        }

        $user->person = null;

        return $this->redirectToRoute('index');
    }

    protected function getAuth()
    {
        $auth = $this->get('session')->get('api_authority_data');
        if (null !== $auth && $auth['expiresAt'] > new \DateTime()) {
            return $auth;
        }
  
        return null;
    }
}
