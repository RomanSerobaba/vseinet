<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Geo\Query;

class RepresentativeController extends Controller
{


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
            return $this->render('AppBundle:Representative:info.html.smarty', [
                'representative' => $representative,
            ]);
        }

        $this->get('query_bus')->handle(new Query\GetDescriptionQuery(['id' => $id]), $description);

        return $this->render('AppBundle:Representative:page.html.smarty', [
            'representative' => $representative,
            'description' => $description,
        ]);
    }
}