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
    public function taggleAction(Request $request)
    {
        $isActive = $this->get('command_bus')->handle(new Command\ToggleCommand($request->request->all()));

        return $this->json([
            'isActive' => $isActive,
        ]);
    }
}
