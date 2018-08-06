<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\DetailValueAudit\Query;

/**
 * @VIA\Section("Характеристики")
 */
class DetailValueAuditController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/details/forAudit/",
     *     description="Получение списка характеристик с новыми значениями",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\DetailValueAudit\Query\GetDetailsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ContentBundle\Bus\DetailValueAudit\Query\DTO\Details")
     *     }
     * )
     */
    public function getDetailsAction()
    {
        $this->get('query_bus')->handle(new Query\GetDetailsQuery(), $details);

        return $details;
    }

    /**
     * @VIA\Get(
     *     path="/detailValues/forAudit/",
     *     description="Получение списка значений характеристики",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\DetailValueAudit\Query\GetDetailValuesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ContentBundle\Bus\DetailValueAudit\Query\DTO\DetailValues")
     *     }
     * )
     */
    public function getDetailValuesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetDetailValuesQuery($request->query->all()), $values);

        return $values;
    } 

}