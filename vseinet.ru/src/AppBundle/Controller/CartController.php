<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Cart\Query;
use AppBundle\Bus\Cart\Command;
use AppBundle\Bus\Favorite\Query\GetInfoQuery as GetFavoriteInfoQuery;
use AppBundle\Bus\Favorite\Command\AddCommand as AddFavoriteCommand;

class CartController extends Controller
{
    /**
     * @VIA\Route(
     *     name="cart",
     *     path="/cart/",
     *     methods={"GET", "POST"}
     * )
     */
    public function getAction(Request $request)
    {
        $cart = $this->get('query_bus')->handle(new Query\GetQuery($request->request->all()));

        if ($request->isXmlHttpRequest()) {
            if ($request->isMethod('POST')) {
                return $this->json([
                    'cart' => $cart,
                ]);
            }

            return $this->json([
                'html' => $this->renderView('Cart/page.html.twig', [
                    'cart' => $cart,
                ]),
            ]);
        }

        return $this->render('Cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    /**
     * @VIA\Get(
     *     name="cart_add",
     *     path="/cart/add/{id}/",
     *     requirements={"id" = "\d+"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer")
     *     }
     * )
     */
    public function addAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\AddCommand($request->query->all(), ['id' => $id]));

        if ($request->isXmlHttpRequest()) {
            $this->get('query_bus')->handle(new Query\GetInfoQuery(), $cart);

            return $this->json([
                'cart' => $cart,
            ]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @VIA\Get(
     *     name="cart_set_quantity",
     *     path="/cart/{id}/quantity/",
     *     requirements={"id" = "\d+"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer")
     *     }
     * )
     */
    public function setQuantityAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetQuantityCommand($request->query->all(), ['id' => $id]));

        if ($request->isXmlHttpRequest()) {
            $this->get('query_bus')->handle(new Query\GetQuery($request->query->all()), $cart);

            return $this->json([
                'cart' => $cart,
            ]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @VIA\Get(
     *     name="cart_dec",
     *     path="/cart/dec/{id}/",
     *     requirements={"id" = "\d+"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer")
     *     }
     * )
     */
    public function decQuantityAction(int $id, Request $request)
    {
        $cart = $this->get('query_bus')->handle(new Query\GetInfoQuery());
        if (!isset($cart->products[$id])) {
            throw new NotFoundHttpException();
        }
        $request->query->set('quantity', $cart->products[$id]->quantity - $cart->products[$id]->minQuantity);

        return $this->setQuantityAction($id, $request);
    }

    /**
     * @VIA\Get(
     *     name="cart_inc",
     *     path="/cart/inc/{id}/",
     *     requirements={"id" = "\d+"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer")
     *     }
     * )
     */
    public function incQuantityAction(int $id, Request $request)
    {
        $cart = $this->get('query_bus')->handle(new Query\GetInfoQuery());
        if (!isset($cart->products[$id])) {
            throw new NotFoundHttpException();
        }
        $request->query->set('quantity', $cart->products[$id]->quantity + $cart->products[$id]->minQuantity);

        return $this->setQuantityAction($id, $request);
    }

    /**
     * @VIA\Get(
     *     name="cart_del",
     *     path="/cart/del/{id}/",
     *     requirements={"id" = "\d+"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer")
     *     }
     * )
     */
    public function deleteAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));

        if ($request->isXmlHttpRequest()) {
            $this->get('query_bus')->handle(new Query\GetQuery($request->query->all()), $cart);

            return $this->json([
                'cart' => $cart,
            ]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @VIA\Get(
     *     name="cart_clear",
     *     path="/cart/clear/"
     * )
     */
    public function clearAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\ClearCommand());
        if ($request->isXmlHttpRequest()) {
            return $this->json([]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @VIA\Get(
     *     name="cart_to_favorite",
     *     path="/cart/{id}/favorite/",
     *     requirements={"id" = "\d+"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer")
     *     }
     * )
     */
    public function toFavoriteAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));
        $this->get('command_bus')->handle(new AddFavoriteCommand(['id' => $id]));

        if ($request->isXmlHttpRequest()) {
            $cart = $this->get('query_bus')->handle(new Query\GetQuery($request->query->all()));
            $favorites = $this->get('query_bus')->handle(new GetFavoriteInfoQuery());

            return $this->json([
                'cart' => $cart,
                'favorites' => $favorites,
            ]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @internal
     */
    public function getInfoAction()
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException();
        }

        $cart = $this->get('query_bus')->handle(new Query\GetInfoQuery());

        return $this->render('Cart/info.html.twig', [
            'cart' => $cart,
        ]);
    }
}
