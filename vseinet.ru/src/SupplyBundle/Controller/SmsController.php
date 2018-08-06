<?php

namespace SupplyBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SupplyBundle\Bus\Sms\Query;
use SupplyBundle\Bus\Sms\Command;

/**
 * @VIA\Description("Смс по заказам")
 * @VIA\Section("Смс по заказам")
 */
class SmsController extends RestController
{    
    /**
     * @VIA\Post(
     *     path="/orders/{id}/sms/",
     *     description="Отправить смс-сообщение",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="SupplyBundle\Bus\Sms\Command\SendCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array")
     *     }
     * )
     */
    public function sendSmsAction(int $id, Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\SendCommand(['id' => $id,], $request->request->all()), ['uuid' => $uuid,]);

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }
}