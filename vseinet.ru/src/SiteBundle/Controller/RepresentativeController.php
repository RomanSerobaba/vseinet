<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SiteBundle\Bus\Geo\Query;

class RepresentativeController extends Controller
{
    /**
     * @VIA\Get(name="contacts", path="/contacts/")
     */
    public function getContactsAction()
    {
        $this->get('query_bus')->handle(new Query\GetContactsQuery(), $contacts);

        return $this->render('SiteBundle:Representative:contacts.html.smarty', [
            'contacts' => $contacts,
        ]);
    }

    /**
     * @VIA\Get(
     *     name="contact_page",
     *     path="/contacts/{id}/",
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer")
     *     }
     * )
     */
    public function getAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $representative);
        if ($request->isXmlHttpRequest()) {
            return $this->render('SiteBundle:Representative:info.html.smarty', [
                'representative' => $representative,
            ]);
        }

        $this->get('query_bus')->handle(new Query\GetDescriptionQuery(['id' => $id]), $description);

        return $this->render('SiteBundle:Representative:page.html.smarty', [
            'representative' => $representative,
            'description' => $description,
        ]);
    }
}