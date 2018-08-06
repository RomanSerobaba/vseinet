<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Bus\User\Command;
use AppBundle\Bus\User\Query;
use AppBundle\Annotation as VIA;

/**
 * @VIA\Section("Пользователи")
 */
class SecurityController extends RestController
{
    /**
     * @VIA\Get(
     *     path="/user/_request/", 
     *     description="Запрос авторизации"
     * )
     * @VIA\Response(
     *      status=200,
     *      properties={
     *          @VIA\Property(name="csrfToken", description="Csrf token")
     *      }
     * )
     */
    public function requestAction()
    {
        return [
            'csrfToken' => $this->getCsrfToken(),
        ];
    }

    /**
     * @VIA\Post(
     *     path="/user/_login/", 
     *     description="Авторизация",
     *     parameters={
     *         @VIA\Parameter(name="X-CSRF-TOKEN", in="header", required=true, description="Csrf token"),
     *         @VIA\Parameter(model="AppBundle\Bus\User\Command\LoginCommand")
     *     }
     * )
     * @VIA\Response(
     *      status=201,
     *      properties={
     *          @VIA\Property(name="accessToken", type="string"),
     *          @VIA\Property(name="expiresIn", type="integer", description="Время жизни Access token-а в секундах"),
     *          @VIA\Property(name="user", model="AppBundle\Entity\User"),
     *      }
     * )
     */
    public function loginAction(Request $request)
    {
        $this->checkCsrfToken($request);
        $this->get('command_bus')->handle(new Command\LoginCommand($request->request->all()));

        $this->get('query_bus')->handle(new Query\GetAccessTokenQuery(), $token);

        return [
            'accessToken' => $token->getToken(),
            'expiresIn' => $token->getExpiresAt() - time(),
            'user' => $token->getUser(),
        ];
    }

    /**
     * @VIA\Get(
     *     path="/user/_refresh/", 
     *     description="Обновление Access token-а",
     *     parameters={    
     *         @VIA\Parameter(name="X-CSRF-TOKEN", in="header", required=true, description="Csrf token"),
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token")
     *     }
     * )
     * @VIA\Response(
     *      status=201,
     *      properties={
     *          @VIA\Property(name="accessToken", description="Access token"),
     *          @VIA\Property(name="expiresIn", type="integer", description="Время жизни Access token-а в секундах"),
     *          @VIA\Property(name="csrfToken", description="Csrf token")
     *      }
     * )
     */
    public function refreshAction(Request $request)
    {
        $this->checkCsrfToken($request);
        $this->get('command_bus')->handle(new Command\RefreshCommand());

        $this->get('query_bus')->handle(new Query\GetAccessTokenQuery(), $token);

        return [
            'accessToken' => $token->getToken(),
            'expiresIn' => $token->getExpiresAt() - time(),
            'csrfToken' => $this->getCsrfToken(true),
        ];
    }

    /**
     * @VIA\Get(
     *     path="/api/v1/user/_logout/", 
     *     description="Выход",
     *     parameters={
     *         @VIA\Parameter(name="AUTHORIZATION", in="header", required=true, description="Bearer token")
     *     }
     * )
     * @VIA\Response(
     *      status=204
     * )
     */
    public function logoutAction()
    {
        $this->get('command_bus')->handle(new Command\LogoutCommand());
    }

    /**
     * @VIA\Post(
     *     path="/_users/",
     *     description="Регистрация пользователя",
     *     parameters={
     *         @VIA\Parameter(name="X-CSRF-TOKEN", in="header", required=true, description="Csrf token"),
     *         @VIA\Parameter(model="AppBundle\Bus\User\Command\RegisterCommand")
     *     }
     * )
     * @VIA\Response(
     *     status=201,
     *     properties={
     *         @VIA\Property(name="id", type="integer")
     *     }
     * )
     */
    public function registerAction(Request $request)
    {
        $this->checkCsrfToken($request);

        $uuid = $this->get('uuid.manager')->generate();
        $this->get('command_bus')->handle(new Command\RegisterCommand($request->request->all(), ['uuid' => $uuid]));

        return [
            'id' => $this->get('uuid.manager')->loadId($uuid)
        ];
    }
}