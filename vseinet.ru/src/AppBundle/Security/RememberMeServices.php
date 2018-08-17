<?php 

namespace AppBundle\Security;

use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class RememberMeServices extends TokenBasedRememberMeServices
{
    /**
     * {@inheritdoc}
     */
    protected function onLoginSuccess(Request $request, Response $response, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user->isEmployee()) {
            parent::onLoginSuccess($request, $response, $token);
        }
    }
}
