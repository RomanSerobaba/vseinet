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
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        $representative = $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]));
        if ($request->isXmlHttpRequest()) {
            return $this->render('AppBundle:Representative:info.html.smarty', [
                'representative' => $representative,
            ]);
        }

        $description = $this->get('query_bus')->handle(new Query\GetDescriptionQuery(['id' => $id]));

        return $this->render('AppBundle:Representative:page.html.smarty', [
            'representative' => $representative,
            'description' => $description,
        ]);
    }
}
