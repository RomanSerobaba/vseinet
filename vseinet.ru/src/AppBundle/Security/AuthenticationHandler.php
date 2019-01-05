<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Constructor
     *
     * @param   RouterInterface $router
     * @param   Session $session
     */
    public function __construct(RouterInterface $router, Session $session)
    {
        $this->router  = $router;
        $this->session = $session;
    }

    /**
     * onAuthenticationSuccess
     *
     * @param   Request $request
     * @param   TokenInterface $token
     *
     * @return  Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $credentials = [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
        ];
        $this->session->set('credentials', $credentials);

        if ($request->isXmlHttpRequest()) {
            $response = new Response(json_encode(['success' => true]));
            $response->headers->set('Content-Type', 'application/json');

            return $response;

        }

        if ($this->session->get('_security.main.target_path')) {
            $url = $this->session->get('_security.main.target_path');
        } else {
            $url = $this->router->generate('index');
        }

        return new RedirectResponse($url);
    }

    /**
     * onAuthenticationFailure
     *
     * @param   Request $request
     * @param   AuthenticationException $exception
     * @return  Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->isXmlHttpRequest()) {
            $response = new Response(json_encode(['error' => $exception->getMessage()]));
            $response->headers->set( 'Content-Type', 'application/json' );

            return $response;
        }

        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse($this->router->generate('login'));
    }
}
