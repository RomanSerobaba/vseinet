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
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Cart;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use AppBundle\Service\GeoCityIdentity;

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
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var TokenStorageInterface
     */
    protected $security;

    /**
     * @var GeoCityIdentity
     */
    protected $geoCityIdentity;

    /**
     * Constructor.
     *
     * @param RouterInterface        $router
     * @param Session                $session
     * @param EntityManagerInterface $em
     */
    public function __construct(
        RouterInterface $router,
        Session $session,
        EntityManagerInterface $em,
        TokenStorageInterface $security,
        GeoCityIdentity $geoCityIdentity
    ) {
        $this->router = $router;
        $this->session = $session;
        $this->em = $em;
        $this->security = $security;
        $this->geoCityIdentity = $geoCityIdentity;
    }

    /**
     * onAuthenticationSuccess.
     *
     * @param Request        $request
     * @param TokenInterface $token
     *
     * @return Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $credentials = [
            'username' => trim($request->request->get('_username')),
            'password' => trim($request->request->get('_password')),
        ];
        $this->session->set('credentials', $credentials);

        $user = $this->security->getToken()->getUser();

        foreach ($this->session->get('cart', []) as $baseProductId => $cartItem) {
            $cart = $this->em->getRepository(Cart::class)->findOneBy(['userId' => $user->getId(), 'baseProductId' => $baseProductId]);

            if (!$cart instanceof Cart) {
                $cart = new Cart();
                $cart->setUserId($user->getId());
                $cart->setBaseProductId($baseProductId);
                $cart->setQuantity(0);
            }

            $cart->setQuantity($cart->getQuantity() + $cartItem['quantity']);
            $this->em->persist($cart);
        }
        $this->em->flush();

        if ($user->isEmployee()) {
            $this->geoCityIdentity->setEmployeeGeoCity($user->getId());
        }

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
     * onAuthenticationFailure.
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->isXmlHttpRequest()) {
            $response = new Response(json_encode(['error' => $exception->getMessage()]));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse($this->router->generate('login'));
    }
}
