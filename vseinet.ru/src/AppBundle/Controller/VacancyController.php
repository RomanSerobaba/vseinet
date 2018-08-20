<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\ContentPage\Query\GetQuery as GetPageQuery;
use AppBundle\Bus\Vacancy\Query;

class VacancyController extends Controller
{
    /**
     * @VIA\Get(name="vacancies_page", path="/vacancies/")
     */
    public function indexAction()
    {
        $this->get('query_bus')->handle(new GetPageQuery(['slug' => 'vacancy']), $page);

        return $this->render('Vacancy/index.html.twig', [
            'page' => $page,
        ]);
    }

    /**
     * @VIA\Get(name="vacancy", path="/vacancies/{id}/")
     */
    public function getAction(int $id, Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]), $vacancy);

        if ($request->isXMLHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('Vacancy/vacancy.html.twig', [
                    'vacancy' => $vacancy,
                ]),
            ]);
        }

        return $this->render('Vacancy/index.html.twig', [
            'vacancy' => $vacancy,
        ]);
    }

    /**
     * @internal 
     */
    public function getListAction()
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }

        $this->get('query_bus')->handle(new Query\GetListQuery(), $list);

        return $this->render('Vacancy/list.html.twig', [
            'list' => $list,
        ]);
    }
}
