<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\Statistics\Query;
use ContentBundle\Bus\Statistics\Command;

/**
 * @VIA\Section("Статистика по контенту")
 */
class StatisticsController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/contentManagers/forFulfillmentStats/",
     *     description="Получение контент-менеджеров",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Statistics\Query\GetManagersQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\Statistics\Query\DTO\ManagerGroup")
     *     }
     * )
     */
    public function getManagersAction()
    {
        $this->get('query_bus')->handle(new Query\GetManagersQuery(), $managers);

        return $managers;
    }

    /**
     * @VIA\Get(
     *     path="/contentFulfillmentStats/",
     *     description="Статистика заполнения контента",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Statistics\Query\GetFulfillmentStatsQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ContentBundle\Bus\Statistics\Query\DTO\FulfillmentStats")
     *     }
     * )
     */
    public function getFulfillmentStatsAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetFulfillmentStatsQuery($request->query->all()), $stats);

        return $stats;
    }

    /**
     * @VIA\Get(
     *     path="/contentFulfillmentSummary/",
     *     description="Сводная статистика заполнения по контент-менеджеру",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Statistics\Query\GetFulfillmentSummaryQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ContentBundle\Bus\Statistics\Query\DTO\FulfillmentSummary")
     *     }
     * )
     */
    public function getFulfillmentSummaryAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetFulfillmentSummaryQuery($request->query->all()), $summary);

        return $summary;
    }


    /**
     * @VIA\Get(
     *     path="/contentFullness/",
     *     description="Заполненность карточек товаров",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Statistics\Query\GetFullnessQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\Statistics\Query\DTO\Fullness")
     *     }
     * )
     * )
     */
    public function getFullnessAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetFullnessQuery($request->query->all()), $fillness);

        return $fillness;
    }

    /**
     * @VIA\Post(
     *     path="/contentFullnessRequest/",
     *     description="Запрос обновления заполненности карточек товаров",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Statistics\Command\FullnessRequestCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function fullnessRequestAction()
    {
        $this->get('command_bus')->handle(new Command\FullnessRequestCommand());
    }
}