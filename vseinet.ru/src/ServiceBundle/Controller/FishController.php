<?php

namespace ServiceBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ServiceBundle\Bus\Fish\Query;

/**
 * @VIA\Section("Рыба")
 */
class FishController extends RestController
{
   /**
     * @VIA\Get(
     *     path="/service/fish/sms/",
     *     description="Получение списка смс-сообщений",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ServiceBundle\Bus\Fish\Query\GetSmsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array")
     *     }
     * )
     */
    public function smsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetSmsQuery($request->query->all()), $list);

        return $list;
    }
}