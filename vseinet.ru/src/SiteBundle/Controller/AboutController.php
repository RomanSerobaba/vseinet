<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Annotation as VIA;
use SiteBundle\Bus\About\Query;

class AboutController extends Controller
{
    /**
     * @VIA\Get(name="about_page", path="/about/")
     */
    public function indexAction()
    {
        $this->get('query_bus')->handle(new Query\GetVacancies(), $vacancies);

        return $this->render('SiteBundle:About:index.html.twig', [
            'vacancies' => $vacancies,
        ]);
    }
}