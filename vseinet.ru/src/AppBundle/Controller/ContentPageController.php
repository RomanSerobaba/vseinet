<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\ContentPage\Query\GetQuery;
use AppBundle\Bus\Vacancy\Query\GetListQuery as GetVacanciesQuery;
use AppBundle\Bus\Category\Query\GetDeliveryTaxesQuery as GetCategoryDeliveryTaxesQuery;
use AppBundle\Bus\Geo\Query\GetDeliveryTaxesQuery as GetRepresentativeDeleiveryTaxesQuery;

class ContentPageController extends Controller
{
    /**
     * @VIA\Get(
     *     name="content_page", 
     *     path="/{slug}/",
     *     requirements={"slug" = "payment|garanty|credit|promo|partnership|help"},
     *     parameters={
     *         @VIA\Parameter(name="slug", type="string")
     *     }
     * )
     */
    public function pageAction($slug)
    {
        return $this->show($slug);
    }

    /**
     * @VIA\Get(
     *     name="about_page", 
     *     path="/about/"
     * )
     */
    public function aboutAction()
    {
        $this->get('query_bus')->handle(new GetVacanciesQuery(), $vacancies);

        return $this->show('about', ['vacancies' => $vacancies]);
    }

    /**
     * @VIA\Get(
     *     name="delivery_page", 
     *     path="/delivery/"
     * )
     */
    public function deliveryAction()
    {
        $this->get('query_bus')->handle(new GetCategoryDeliveryTaxesQuery(), $deliveryTaxes);
        $this->get('query_bus')->handle(new GetRepresentativeDeleiveryTaxesQuery(), $representatives);

        return $this->show('delivery', ['deliveryTaxes' => $deliveryTaxes, 'representatives' => $representatives]);
    }

    protected function show($slug, array $data = [])
    {
        $this->get('query_bus')->handle(new GetQuery(['slug' => $slug]), $page);
        $template = empty($data) ? 'page' : $slug; 

        return $this->render("ContentPage/{$template}.html.twig", $data + ['page' => $page]);  
    }
}
