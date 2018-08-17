<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Suggestion\Command;

class SuggestionController extends Controller
{
    /**
     * @VIA\Get(
     *     name="suggestion", 
     *     path="/suggestion/",
     *     methods={"GET", "POST"}
     * )
     */
    public function indexAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $this->get('command_bus')->handle(new Command\HandleCommand($request->request->all()));

            return $this->redirectToRoute('suggestion_success');     
        }

        return $this->render('Suggestion/form.html.twig');
    }

    /**
     * @VIA\Get(
     *     name="suggestion_success", 
     *     path="/suggestion/success/"
     * )
     */
    public function successAction()
    {
        return $this->render('Suggestion/success.html.twig');
    }
}
