<?php 

namespace ContentBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ContentBundle\Bus\ParserSource\Query;
use ContentBundle\Bus\ParserSource\Command;

/**
 * @VIA\Section("Парсеры")
 */
class ParserSourceController extends RestController
{
    /**
     * @VIA\Get(
     *      path="/parserSources/",
     *      description="Получение списка источников парсинга",
     *      parameters={
     *          @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *          @VIA\Parameter(model="ContentBundle\Bus\ParserSource\Query\GetListQuery")
     *      }
     * )
     * @VIA\Response(
     *      status=200,
     *      properties={
     *          @VIA\Property(type="array", model="ContentBundle\Bus\ParserSource\Query\DTO\Source")
     *      }
     * )
     */
    public function getListAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetListQuery($request->query->all()), $sources);

        return $sources;
    }

    /**
     * @VIA\Get(
     *     path="/parserSources/{id}/",
     *     description="Получение источника парсинга",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\ParserSource\Query\GetQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ContentBundle\Entity\ParserSource")
     *     }
     * )
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $source);

        return $source;
    }

    /**
     * @deprecated
     * @VIA\Get(
     *     path="/parserSources/{id}/code/",
     *     description="Получение исходного кода источника парсинга",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\ParserSource\Query\GetCodeQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(type="array", model="ContentBundle\Bus\ParserSource\Query\DTO\Code")
     *     }
     * )
     */
    public function getCodeAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetCodeQuery(['id' => $id]), $codes);

        return $codes;
    }

    /**
     * @VIA\Post(
     *     path="/parserSources/",
     *     description="Создание источника парсинга",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\ParserSource\Command\CreateCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function newAction(Request $request)
    {
        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\CreateCommand($request->request->all(), ['uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid),
        ];
    }

    /**
     * @VIA\Put(
     *     path="/parserSources/{id}/",
     *     description="Редактирование источника парсинга",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\ParserSource\Command\UpdateCommand")
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
     * @VIA\Put(
     *     path="/parserSources/{id}/isActive/",
     *     description="Включение / отключение источника парсинга",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\ParserSource\Command\SetIsActiveCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function setIsActiveAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetIsActiveCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @deprecated
     * @VIA\Put(
     *     path="/parserSources/{id}/sortOrder/",
     *     description="Сортировка источников парсинга",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\ParserSource\Command\SortCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function sortAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SortCommand($request->request->all(), ['id' => $id]));
    }

    /**
     * @VIA\Delete(
     *     path="/parserSources/{id}/",
     *     description="Удаление истоника прасинга",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\ParserSource\Command\DeleteCommand")
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

    /**
     * @deprecated
     * @VIA\Post(
     *     path="/parserSources/{id}/",
     *     description="Отправка исходного кода истоника прасинга клиентам",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token"),
     *         @VIA\Parameter(model="ContentBundle\Bus\ParserSource\Command\SendCodeCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function sendCodeAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\SendCodeCommand(['id' => $id]));
    }
}