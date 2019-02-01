<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use AppBundle\Bus\Exception\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionController extends \Symfony\Bundle\TwigBundle\Controller\ExceptionController
{
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        if (Response::HTTP_NOT_FOUND == $exception->getStatusCode()) {
            return new Response($this->twig->render(
                'Exception/error404.html.twig'
            ), Response::HTTP_NOT_FOUND, array('Content-Type' => $request->getMimeType($request->getRequestFormat()) ?: 'text/html'));
        }

        return parent::showAction($request, $exception, $logger);
    }
}
