<?php 

namespace AppBundle\Controller;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use AppBundle\Bus\Exception\ValidationException;

class ExceptionController extends RestController
{
    public function showAction(Request $request, $exception, DebugLoggerInterface $logger = null)
    {
        if ($exception instanceof ValidationException) {
            return new View(['errors' => $exception->getMessages()], Response::HTTP_BAD_REQUEST);
        }

        if (method_exists($exception, 'getStatusCode')) {
            $code = $exception->getStatusCode();
        }
        else {
            $code = $exception->getCode() ?: Response::HTTP_BAD_REQUEST;
        }

        $content['error'] = $exception->getMessage(); 

        // if (false !== $pos = strpos($exception->getFile(), '/src/')) {
        //     $content['file'] = substr($exception->getFile(), $pos + 4);
        //     $content['line'] = $exception->getLine();
        // }

        return new View($content, $code);
    }
} 