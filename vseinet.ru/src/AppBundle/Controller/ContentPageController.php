<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\ContentPage\Query\GetQuery;
use AppBundle\Bus\Category\Query\GetDeliveryTaxesQuery as GetCategoryDeliveryTaxesQuery;
use AppBundle\Bus\Geo\Query\GetDeliveryTaxesQuery as GetRepresentativeDeleiveryTaxesQuery;
use AppBundle\Bus\Vacancy\Query\GetListQuery;

class ContentPageController extends Controller
{
    /**
     * @VIA\Get(
     *     name="content_page",
     *     path="/{slug}/",
     *     requirements={"slug"="payment|garanty|credit|promo|partnership|help|service"},
     *     parameters={
     *         @VIA\Parameter(name="slug", type="string")
     *     }
     * )
     */
    public function pageAction($slug, Request $request)
    {
        return $this->show($slug, $request->query->all());
    }

    /**
     * @VIA\Get(
     *     name="about_page",
     *     path="/about/"
     * )
     */
    public function aboutAction()
    {
        $vacancies = $this->get('query_bus')->handle(new GetListQuery());

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
        $deliveryTaxes = $this->get('query_bus')->handle(new GetCategoryDeliveryTaxesQuery());
        $representatives = $this->get('query_bus')->handle(new GetRepresentativeDeleiveryTaxesQuery());

        return $this->show('delivery', ['deliveryTaxes' => $deliveryTaxes, 'representatives' => $representatives]);
    }

    protected function show($slug, array $data = [])
    {
        if ('about' === $slug) {
            return $this->render("ContentPage/about.html.twig", $data);
        }

        $page = $this->get('query_bus')->handle(new GetQuery(['slug' => $slug, 'id' => $data['id'] ?? null]));
        $template = empty($data) || 'service' === $slug ? 'page' : $slug;

        return $this->render("ContentPage/{$template}.html.twig", $data + ['page' => $page]);
    }
}
