<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SiteBundle\Bus\Main\Query;

class MainController extends Controller
{
    /**
     * @VIA\Get(name="index", path="/")
     */
    public function indexAction()
    {
        return $this->render('SiteBundle:Main:index.html.twig');
    }

    /**
     * @VIA\Get(name="menu", path="/menu/")
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

        return $this->render('SiteBundle:Main:menu.html.twig', [
            'menu' => $menu,
        ]);
    }

    /**
     * @VIA\Get(name="banner_main", path="/banner/main/")
     */
    public function getBannerMainAction()
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }

        $this->get('query_bus')->handle(new Query\GetBannerMainQuery(), $data);

        return $this->render('SiteBundle:Main:banner_main.html.twig', $data);
    }

    /**
     * @VIA\Get(name="block_specials", path="/block/specials/{categoryId}/", requirements={"categoryId" = "\d*"})
     */
    public function getBlockSpecialsAction(int $categoryId = 0)
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }

        $this->get('query_bus')->handle(new Query\GetBlockSpecialsQuery(['categoryId' => $categoryId, 'count' => 6]), $products);

        return $this->render('SiteBundle:Main:block_specials.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @VIA\Get(name="block_populars", path="/block/populars/")
     */
    public function getBlockPopularsAction()
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }

        $this->get('query_bus')->handle(new Query\GetBlockPopularsQuery(['count' => 4]), $products);

        return $this->render('SiteBundle:Main:block_populars.html.twig', [
            'products' => $products,
        ]);  
    }

    /**
     * @VIA\Get(name="block_lastview", path="/block/lastview")
     */
    public function getBlockLastviewAction()
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }

        $this->get('query_bus')->handle(new Query\GetBlockLastviewQuery(['count' => 4]), $products);

        return $this->render('SiteBundle:Main:block_lastview.html.twig', [
            'products' => $products,
        ]);          
    }
}
