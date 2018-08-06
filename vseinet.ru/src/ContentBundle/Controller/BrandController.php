<?php

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\Brand\Query;
use ContentBundle\Bus\Brand\Command;

/**
 * @VIA\Section("Бренды")
 */
class BrandController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/brands/AlphabetIndex/",
     *     description="Получение алфавитного указателя",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Brand\Query\GetAlphabetIndexQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\Brand\Query\DTO\AlphabetIndex")
     *     }
     * )
     */
    public function getAlphaIndexAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetAlphabetIndexQuery($request->query->all()), $index);

        return $index;
    }

    /**
     * @VIA\Get(
     *     path="/brands/", 
     *     description="Получение списка брендов",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Brand\Query\GetListQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\Brand\Query\DTO\Brand")
     *     }
     * )
     */
    public function getListAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetListQuery($request->query->all()), $brands);

        return $brands;
    }    

    /**
     * @VIA\Get(
     *     path="/brands/foundResults/", 
     *     description="Поиск брендов по названию",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Brand\Query\SearchQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\Brand\Query\DTO\FoundResult")
     *     }
     * )
     */
    public function searchAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\SearchQuery($request->query->all()), $results);

        return $results;
    }

    /**
     * @VIA\Get(
     *     path="/brands/{id}/pseudos/",
     *     description="Получение списка пседонимов бренда",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Brand\Query\GetPseudosQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Parameter(type="array", model="ContentBundle\Entity\BrandPseudo")
     *     }
     * )
     */
    public function getPseudosAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetPseudosQuery(['id' => $id]), $pseudos);

        return $pseudos;
    }

    /**
     * @VIA\Get(
     *     path="/brands/{id}/", 
     *     description="Получение бренда",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Brand\Query\GetQuery"),    
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ContentBundle\Bus\Brand\Query\DTO\Brand")
     *     }
     * )
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $brand);

        return $brand;
    }

    /**
     * @VIA\Put(
     *     path="/brands/{id}/",
     *     description="Редактирование бренда",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Brand\Command\UpdateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function editAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\UpdateCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Patch(
     *     path="/brands/{id}/",
     *     description="Объединение брендов",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Brand\Command\MergeCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function mergeAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\MergeCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Put(
     *     path="/brands/{id}/isForbidden/",
     *     description="Показывать / скрывать бренд",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Brand\Command\SetIsForbiddenCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setIsForbiddenAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetIsForbiddenCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Delete(
     *     path="/brands/{id}/",
     *     description="Удаление бренда",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\Brand\Command\DeleteCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function removeAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));
    }
}