<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\ContentPage\Bus\Query\GetQuery as GetPageQuery;
use AppBundle\Bus\Vacancy\Query;

class VacancyController extends Controller
{
    /**
     * @VIA\Get(name="vacancies_page", path="/vacancies/")
     */
    public function indexAction()
    {
        $this->get('query_bus')->handle(new GetPageQuery(['slug' => 'vacancy']), $page);

        return $this->render('Vacancy/index.html.smarty', [
            'page' => $page,
        ]);
    }

    /**
     * @VIA\Get(name="vacancy", path="/vacancies/{id}/")
     */
    public function getAction(int $id)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $vacancy);

        return $this->render('Vacancy/vacancy.html.twig', [
            'vacancy' => $vacancy,
        ]);
    }

    /**
     * @internal 
     */
    public function listAction()
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }

        $this->get('query_bus')->handle(new Query\GetListQuery(), $list);

        return $this->render('Vacancy/list.html.smarty', [
            'list' => $list,
        ]);
    }
}
