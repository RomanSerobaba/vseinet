<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Main\Query;

class MainController extends Controller
{
    /**
     * @VIA\Get(
     *     name="index", 
     *     path="/"
     * )
     */
    public function indexAction()
    {
        return $this->render('Main/index.html.twig');
    }

    /**
     * @internal 
     */
    public function getMenuAction()
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }

        $this->get('query_bus')->handle(new Query\GetMenuQuery(), $menu);
        foreach ($menu as &$item) {
            $this->get('query_bus')->handle(new Query\GetBlockSpecialsQuery(['categoryId' => $item->id, 'count' => 1]), $products);
            if (!empty($products)) {
                $item->product = reset($products);
            }
        }

        return $this->render('Main/menu.html.twig', [
            'menu' => $menu,
        ]);
    }

    /**
     * @internal 
     */
    public function getBannerMainAction()
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }

        $this->get('query_bus')->handle(new Query\GetBannerMainQuery(), $data);

        return $this->render('Main/banner_main.html.twig', $data);
    }

    /**
     * @internal 
     */
    public function getBlockSpecialsAction(int $categoryId = 0)
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }

        $this->get('query_bus')->handle(new Query\GetBlockSpecialsQuery(['categoryId' => $categoryId, 'count' => 6]), $products);

        return $this->render('Main/block_specials.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @internal 
     */
    public function getBlockPopularsAction()
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }

        $this->get('query_bus')->handle(new Query\GetBlockPopularsQuery(['count' => 4]), $products);

        return $this->render('Main/block_populars.html.twig', [
            'products' => $products,
        ]);  
    }

    /**
     * @internal 
     */
    public function getBlockLastviewAction()
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }

        $this->get('query_bus')->handle(new Query\GetBlockLastviewQuery(['count' => 4]), $products);

        return $this->render('Main/block_lastview.html.twig', [
            'products' => $products,
        ]);          
    }
}
