<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Annotation as VIA;
use SiteBundle\Bus\Delivery\Query;

class DeliveryController extends Controller
{
    /**
     * @VIA\Get(name="delivery_page", path="/delivery/")
     */
    public function indexAction()
    {

        return $this->render('SiteBundle:Delivery:index.html.twig');
    }
}