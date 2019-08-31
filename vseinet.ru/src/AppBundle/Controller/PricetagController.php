<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Pricetags\Command;

class PricetagController extends Controller
{
    /**
     * @VIA\Post(
     *     name="toggle_pricetag",
     *     path="/pricetags/",
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function toggleAction(Request $request)
    {
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        $isActive = $this->get('command_bus')->handle(new Command\ToggleCommand($request->request->all()));

        return $this->json([
            'isActive' => $isActive,
        ]);
    }
}
