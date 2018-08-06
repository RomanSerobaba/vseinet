<?php

namespace ShopBundle\Controller;

use AppBundle\Controller\RestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use ShopBundle\Bus\User\Query;
use ShopBundle\Bus\User\Command;

/**
 * @VIA\Section("Магазин:Личный кабинет")
 */
class UserController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/user/account/",
     *     description="Получить данные аккаунта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\User\Query\GetAccountQuery")
     *     }
     * )
     * @VIA\Response(
     *     status=200,
     *     properties={
     *         @VIA\Property(model="ShopBundle\Bus\User\Query\DTO\Account", type="object")
     *     }
     * )
     */
    public function getBannerAction()
    {
        $this->get('query_bus')->handle(new Query\GetAccountQuery(), $account);

        return $account;
    }

    /**
     * @VIA\Post(
     *     path="/user/account/",
     *     description="Сохранить данные аккаунта",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\User\Command\EditCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function editAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\EditCommand($request->request->all()));
    }

    /**
     * @VIA\Post(
     *     path="/user/password/",
     *     description="Смена пароля",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=false, description="Bearer token"),
     *         @VIA\Parameter(model="ShopBundle\Bus\User\Command\PasswordCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=204
     * )
     */
    public function passwordAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\PasswordCommand($request->request->all()));
    }
}
