<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use SiteBundle\Bus\ContentPage\Bus\Query\GetQuery as GetPageQuery;
use SiteBundle\Bus\Vacancy\Query;

class VacancyController extends Controller
{
    /**
     * @VIA\Get(name="vacancies", path="/vacancies/")
     */
    public function indexAction()
    {
        $this->get('query_bus')->handle(new GetPageQuery(['slug' => 'vacancy']), $page);

        return $this->render('SiteBundle:Vacancy:index.html.smarty', [
            'page' => $page,
        ]);
    }

    /**
     * @VIA\Get(name="vacancy_page", path="/vacancies/{id}/")
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $vacancy);

        return $this->render('SiteBundle:Vacancy:vacancy.html.twig', [
            'vacancy' => $vacancy,
        ]);
    }

    /**
     * @internal vacancies list.
     */
    public function listAction()
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }

        $this->get('query_bus')->handle(new Query\GetListQuery(), $list);

        return $this->render('SiteBundle:Vacancy:list.html.smarty', [
            'list' => $list,
        ]);
    }
}
