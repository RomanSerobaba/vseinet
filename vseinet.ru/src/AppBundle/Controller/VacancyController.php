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
        $page = $this->get('query_bus')->handle(new GetPageQuery(['slug' => 'vacancy']));

        return $this->render('Vacancy/index.html.twig', [
            'page' => $page,
        ]);
    }

    /**
     * @VIA\Get(name="vacancy", path="/vacancies/{id}/")
     */
    public function getAction(int $id, Request $request)
    {
        $vacancy = $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]));

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

        $list = $this->get('query_bus')->handle(new Query\GetListQuery());

        return $this->render('Vacancy/list.html.twig', [
            'list' => $list,
        ]);
    }
}
