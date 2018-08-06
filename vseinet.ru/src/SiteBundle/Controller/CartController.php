<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SiteBundle\Bus\Cart\Query;
use SiteBundle\Bus\Cart\Command;
use SiteBundle\Bus\Favorite\Query\GetInfoQuery as GetFavoriteInfoQuery;
use SiteBundle\Bus\Favorite\Command\AddCommand as AddFavoriteCommand;

class CartController extends Controller
{
    /**
     * @VIA\Get(name="cart", path="/cart/")
     */
    public function getAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(), $cart);

        if ($request->isXMLHttpRequest()) {
            return $this->render('SiteBundle:Cart:page.html.twig', ['cart' => $cart]);
        }

        return $this->render('SiteBundle:Cart:index.html.twig', ['cart' => $cart]);
    }

    /**
     * @VIA\Get(name="cart_add", path="/cart/add/{id}/", requirements={"id" = "\d+"})
     */
    public function addAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\AddCommand($request->query->all(), ['id' => $id])); 
        if ($request->isXmlHttpRequest()) {
            $this->get('query_bus')->handle(new Query\GetInfoQuery(), $cart);

            return $this->json(['cart' => $cart]);
        }
          
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @VIA\Get(name="cart_set_quantity", path="/cart/{id}/quantity/", requirements={"id" = "\d+"})
     */
    public function setQuantityAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetQuantityCommand($request->query->all(), ['id' => $id]));
        if ($request->isXmlHttpRequest()) {
            $this->get('query_bus')->handle(new Query\GetInfoQuery(), $cart);

            return $this->json(['cart' => $cart]);
        } 

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @VIA\Get(name="cart_dec", path="/cart/dec/{id}/", requirements={"id" = "\d+"})
     */
    public function decQuantityAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetInfoQuery(), $cart);
        if (!isset($cart->products[$id])) {
            throw new NotFoundHttpException();
        }    
        $request->query->set('quantity', $cart->products[$id]->quantity - 1);

        return $this->setQuantityAction($id, $request);
    }

    /**
     * @VIA\Get(name="cart_inc", path="/cart/inc/{id}/", requirements={"id" = "\d+"})
     */
    public function incQuantityAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetInfoQuery(), $cart);
        if (!isset($cart->products[$id])) {
            throw new NotFoundHttpException();
        }    
        $request->query->set('quantity', $cart->products[$id]->quantity + 1);

        return $this->setQuantityAction($id, $request);
    }

    /**
     * @VIA\Get(name="cart_del", path="/cart/{id}/del/", requirements={"id" = "\d+"})
     */
    public function deleteAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));
        if ($request->isXmlHttpRequest()) {
            $this->get('query_bus')->handle(new Query\GetInfoQuery(), $cart);

            return $this->json(['cart' => $cart]);
        }  

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @VIA\Get(name="cart_clear", path="/cart/clear/")
     */
    public function clearAction()
    {
        $this->get('command_bus')->handle(new Command\ClearCommand());
        if ($this->isXmlHttpRequest()) {
            return $this->json();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @VIA\Get(name="cart_to_favorite", path="/cart/{id}/favorite/", requirements={"id" = "\d+"})
     */
    public function toFavoriteAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteCommand(['id' => $id]));
        $this->get('command_bus')->handle(new AddFavoriteCommand(['id' => $id]));
        if ($request->isXMLHttpRequest()) {
            $this->get('query_bus')->handle(new Query\GetInfoQuery(), $cart);    
            $this->get('query_bus')->handle(new GetFavoriteInfoQuery(), $favorites);    

            return $this->json(['cart' => $cart, 'favorites' => $favorites]);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @VIA\Get(name="cart_info", path="/cart/info/")
     */
    public function getInfoAction()
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException();
        }

        $this->get('query_bus')->handle(new Query\GetInfoQuery(), $info);

        return $this->render('SiteBundle:Cart:info.html.twig', ['info' => $info]);
    }
}
