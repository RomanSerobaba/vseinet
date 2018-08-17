<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Favorite\Query;
use AppBundle\Bus\Favorite\Command;
use AppBundle\Bus\Cart\Query\GetInfoQuery as GetCartInfoQuery;
use AppBundle\Bus\Cart\Command\AddCommand as AddCartCommand;

class FavoriteController extends Controller
{
    /**
     * @VIA\Get(
     *     name="favorite", 
     *     path="/favorite/"
     * )
     */
    public function getAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(), $favorites);

        if ($request->isXMLHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('Favorite/page.html.twig', [
                    'favorites' => $favorites,
                ]),
            ]);
        }

        return $this->render('Favorite/index.html.twig', [
            'favorites' => $favorites,
        ]);
    }

    /**
     * @VIA\Get(
     *     name="favorite_add", 
     *     path="/favorite/add/{id}/", 
     *     requirements={"id" = "\d+"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer") 
     *     }
     * )
     */
    public function addAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\AddCommand(['id' => $id]));
        
        if ($request->isXMLHttpRequest()) {
            $this->get('query_bus')->handle(new Query\GetInfoQuery(), $favorites);
            
            return $this->json([
                'favorites' => $favorites,
            ]);
        }
        
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @VIA\Get(
     *     name="favorite_del", 
     *     path="/favorite/del/{id}/", 
     *     requirements={"id" = "\d+"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer") 
     *     }
     * )
     */
    public function deleteAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));

        if ($request->isXMLHttpRequest()) {
            $this->get('query_bus')->handle(new Query\GetInfoQuery(), $favorites);
            
            return $this->json([
                'favorites' => $favorites,
            ]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @VIA\Get(
     *     name="favorite_clear", 
     *     path="/favorite/clear/"
     * )
     */
    public function clearAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\ClearCommand());

        if ($request->isXMLHttpRequest()) {
            return $this->json([]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @VIA\Get(
     *     name="favorite_to_cart", 
     *     path="/favorite/{id}/cart/", 
     *     requirements={"id" = "\d+"},
     *     parameters={
     *         @VIA\Parameter(name="id", type="integer") 
     *     }
     * )
     */
    public function toCartAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));
        $this->get('command_bus')->handle(new AddCartCommand(['id' => $id]));

        if ($request->isXMLHttpRequest()) {
            $this->get('query_bus')->handle(new Query\GetInfoQuery(), $cart);    
            $this->get('query_bus')->handle(new GetFavoriteInfoQuery(), $favorites);    

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
    public function getInfoAction(Request $request)
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }
        
        $this->get('query_bus')->handle(new Query\GetInfoQuery(), $favorites);

        return $this->render('Favorite/info.html.twig', [
            'favorites' => $favorites,
        ]);
    }
}
