<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;

class ContentPageController extends Controller
{
    /**
     * @VIA\Get(name="content_page", path="/pages/{slug}/")
     */
    public function getAction($slug)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['slug' => $slug]), $page);

        return $this->render('SiteBundle:ContentPage:page.html.smarty', [
            'page' => $page,
        ]);
    }

    /**
     * @internal content pages list.
     */
    public function listAction($type)
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }

        $this->get('query_bus')->handle(new Query\GetListQuery(['type' => $type]), $result);
        $result->slug = $masterRequest->query->get('slug', '');

        return $this->render('SiteBundle:ContentPage:list.html.smarty', $result);
    }
}
