<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SiteBundle\Bus\Suggestion\Command;

class SuggestionController extends Controller
{
    /**
     * @VIA\Get(name="suggestion_form", path="/suggestion/")
     */
    public function indexAction()
    {
        return $this->render('SiteBundle:Suggestion:form.html.twig');
    }

    /**
     * @VIA\Post(name="suggestion_handle", path="/suggestion/")
     */
    public function handleAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\HandleCommand($request->request->all()));

        return $this->redirectToRoute('suggestion_success');
    }

    /**
     * @VIA\Get(name="suggestion_success", path="/suggestion/success/")
     */
    public function successAction()
    {
        return $this->render('SiteBundle:Suggestion:success.html.twig');
    }
}