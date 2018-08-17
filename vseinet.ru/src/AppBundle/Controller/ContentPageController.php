<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;

class ContentPageController extends Controller
{
    /**
     * @VIA\Get(
     *     name="content_page", 
     *     path="/pages/{slug}/",
     *     requirements={"slug" = "[^\/]*"},
     *     parameters={
     *         @VIA\Parameter(name="slug", type="string")
     *     }
     * )
     */
    public function getAction($slug)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['slug' => $slug]), $page);

        return $this->render('ContentPage/page.html.smarty', [
            'page' => $page,
        ]);
    }

    /**
     * @internal
     * @deprecated
     */
    public function listAction($type)
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }

        $this->get('query_bus')->handle(new Query\GetListQuery(['type' => $type]), $result);
        $result->slug = $masterRequest->query->get('slug', '');

        return $this->render('ContentPage/list.html.smarty', $result);
    }

    /**
     * @VIA\Get(
     *     name="about_page", 
     *     path="/about/"
     * )
     */
    public function aboutAction()
    {
        $this->get('query_bus')->handle(new Query\GetVacanciesQuery(), $vacancies);

        return $this->render('ContentPage/about.html.twig', [
            'vacancies' => $vacancies,
        ]);
    }

    /**
     * @VIA\Get(
     *     name="delivery_page", 
     *     path="/delivery/"
     * )
     */
    public function deliveryAction()
    {

        return $this->render('ContentPage/delivery.html.twig');
    }

    /**
     * @VIA\Get(
     *     name="payment_page",
     *     path="/payment/"
     * )
     */
    public function paymentAction()
    {
        
    }
}
