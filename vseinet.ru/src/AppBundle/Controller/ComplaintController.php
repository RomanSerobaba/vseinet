<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Complaint\Command;

class ComplaintController extends Controller
{
    /**
     * @VIA\Get(
     *     name="complaint", 
     *     path="/complaint/",
     *     methods={"GET", "POST"}
     * )
     */
    public function indexAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $this->get('command_bus')->handle(new Command\HandleCommand($request->request->all()));

            return $this->redirectToRoute('complaint_success');
        }

        return $this->render('Complaint/form.html.twig');
    }

    /**
     * @VIA\Get(
     *     name="complaint_success", 
     *     path="/complaint/success/"
     * )
     */
    public function successAction()
    {
        return $this->render('Complaint/success.html.twig');
    }
}
