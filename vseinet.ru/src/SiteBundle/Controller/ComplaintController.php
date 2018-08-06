<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SiteBundle\Bus\Complaint\Command;

class ComplaintController extends Controller
{
    /**
     * @VIA\Get(name="complaint_form", path="/complaint/")
     */
    public function indexAction()
    {
        return $this->render('SiteBundle:Complaint:form.html.twig');
    }

    /**
     * @VIA\Post(name="complaint_handle", path="/complaint/")
     */
    public function handleAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\HandleCommand($request->request->all()));

        return $this->redirectToRoute('complaint_success');
    }

    /**
     * @VIA\Get(name="complaint_success", path="/complaint/success/")
     */
    public function successAction()
    {
        return $this->render('SiteBundle:Complaint:success.html.twig');
    }
}