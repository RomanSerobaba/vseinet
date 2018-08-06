<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SiteBundle\Bus\Favorite\Query;
use SiteBundle\Bus\Favorite\Command;
use SiteBundle\Bus\Cart\Query\GetInfoQuery as GetCartInfoQuery;
use SiteBundle\Bus\Cart\Command\AddCommand as AddCartCommand;

class FavoriteController extends Controller
{
    /**
     * @VIA\Get(name="favorite", path="/favorite/")
     */
    public function getAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(), $favorites);

        if ($request->isXMLHttpRequest()) {
            return $this->render('SiteBundle:Favorite:page.html.twig', ['favorites' => $favorites]);
        }

        return $this->render('SiteBundle:Favorite:index.html.twig', ['favorites' => $favorites]);
    }

    /**
     * @VIA\Get(name="favorite_add", path="/favorite/add/{id}/", requirements={"id" = "\d+"})
     */
    public function addAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\AddCommand(['id' => $id]));
        if ($request->isXMLHttpRequest()) {
            $this->get('query_bus')->handle(new Query\GetInfoQuery(), $favorites);
            
            return $this->json(['favorites' => $favorites]);
        }
        
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @VIA\Get(name="favorite_del", path="/favorite/del/{id}/", requirements={"id" = "\d+"})
     */
    public function deleteAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));
        if ($request->isXMLHttpRequest()) {
            $this->get('query_bus')->handle(new Query\GetInfoQuery(), $favorites);
            
            return $this->json(['favorites' => $favorites]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @VIA\Get(name="favorite_clear", path="/favorite/clear/")
     */
    public function clearAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\ClearCommand());
        if ($request->isXMLHttpRequest()) {
            return $this->json();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @VIA\Get(name="favorite_to_cart", path="/favorite/{id}/cart/", requirements={"id" = "\d+"})
     */
    public function toCartAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));
        $this->get('command_bus')->handle(new AddCartCommand(['id' => $id]));
        if ($request->isXMLHttpRequest()) {
            $this->get('query_bus')->handle(new Query\GetInfoQuery(), $cart);    
            $this->get('query_bus')->handle(new GetFavoriteInfoQuery(), $favorites);    

            return $this->json(['cart' => $cart, 'favorites' => $favorites]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @VIA\Get(name="favorite_info", path="/favorite/info/")
     */
    public function getInfoAction(Request $request)
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }
        
        $this->get('query_bus')->handle(new Query\GetInfoQuery(), $favorites);

        return $this->render('SiteBundle:Favorite:info.html.twig', ['favorites' => $favorites]);
    }
}
