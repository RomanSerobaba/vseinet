<?php

namespace PricingBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use PricingBundle\Bus\Competitors\Query;
use PricingBundle\Bus\Competitors\Command;

/**
 * @VIA\Section("Сверка с конкурентами")
 */
class CompetitorsController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/competitors/",
     *     description="Получить список конкурентов",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="PricingBundle\Bus\Competitors\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="PricingBundle\Bus\Competitors\Query\DTO\GetList")
     *     }
     * )
     */
    public function listAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetListQuery($request->query->all()), $index);

        return $index;
    }

    /**
     * @VIA\Get(
     *     path="/competitors/{id}/revision/",
     *     description="Получить сверку",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="PricingBundle\Bus\Competitors\Query\GetRevisionQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", name="products", model="PricingBundle\Bus\Competitors\Query\DTO\RevisionProducts"),
     *         @VIA\Property(type="array", name="categories", model="PricingBundle\Bus\Competitors\Query\DTO\RevisionCategories"),
     *         @VIA\Property(type="integer", name="total")
     *     }
     * )
     */
    public function getRevisionAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetRevisionQuery($request->query->all(), ['id' => $id]), $index);

        return $index;
    }

    /**
     * @VIA\Get(
     *     path="/competitors/export/",
     *     description="Выгрузка сверки в формате Excel",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="PricingBundle\Bus\Competitors\Command\ExportCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="file")
     *     }
     * )
     */
    public function exportAction(Request $request)
    {
        $fileName = $this->getParameter('competitors.export.path') . DIRECTORY_SEPARATOR . 'Сверка '.date('d-m-Y').'.xls';

        if (!file_exists($this->getParameter('competitors.export.path'))) {
            mkdir($this->getParameter('competitors.export.path'), 0777, true);
        }

        $this->get('command_bus')->handle(new Command\ExportCommand($request->query->all(), ['fileName' => $fileName,]));

        return $this->file($fileName);
    }

    /**
     * @VIA\Get(
     *     path="/cities/forCompetitors/",
     *     description="Получить список регионов для сверки с конкурентами",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="PricingBundle\Bus\Competitors\Query\GetCitiesQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="PricingBundle\Bus\Competitors\Query\DTO\Cities")
     *     }
     * )
     */
    public function getCitiesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetCitiesQuery($request->query->all()), $index);

        return $index;
    }

    /**
     * @VIA\Put(
     *     path="/competitors/{id}/",
     *     description="Редактировать конкурента",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="PricingBundle\Bus\Competitors\Command\EditCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function editAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\EditCommand($request->request->all(), ['id' => $id,]));
    }

    /**
     * @VIA\Post(
     *     path="/competitors/",
     *     description="Добавить конкурента",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="PricingBundle\Bus\Competitors\Command\AddCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function addAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\AddCommand($request->request->all()));
    }

    /**
     * @VIA\Put(
     *     path="/competitors/{id}/active/",
     *     description="Активировать/деактивировать конкурента",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="PricingBundle\Bus\Competitors\Command\ToggleIsActiveCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function toggleIsActiveAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\ToggleIsActiveCommand($request->request->all(), ['id' => $id,]));
    }
}