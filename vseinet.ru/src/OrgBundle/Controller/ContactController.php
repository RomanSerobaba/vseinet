<?php

namespace OrgBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use OrgBundle\Bus\Contact\Query;

/**
 * @VIA\Section("Структура организации - Контакты")
 */
class ContactController extends RestController
{
    
    /**
     * @VIA\Get(
     *     path="/officeNumbers/",
     *     description="Получить информацию о телефонных номерах организации",
     *     parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="OrgBundle\Bus\Contact\Query\GetOfficeNumbersQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="OrgBundle\Bus\Contact\Query\DTO\ContactInfo")
     *     }
     * )
     */
    public function GetOfficeNumbersAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetOfficeNumbersQuery($request->query->all()), $items);

        return $items;
    }
}
