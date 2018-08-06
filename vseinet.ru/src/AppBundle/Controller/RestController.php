<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RestController extends FOSRestController
{
    protected function getCsrfToken(bool $regenerate = false)
    {
        if ($regenerate) {
            $this->get('security.csrf.token_manager')->refreshToken('authenticate');
        }

        return $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
    }

    protected function checkCsrfToken(Request $request)
    {
        $csrf = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
        if ($csrf !== $request->headers->get('X-CSRF-TOKEN')) {
            throw new BadRequestHttpException('CSRF detect!!!');
        }
    }
}