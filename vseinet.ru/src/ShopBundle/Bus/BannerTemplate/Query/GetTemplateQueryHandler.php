<?php 

namespace ShopBundle\Bus\BannerTemplate\Query;

use AppBundle\Bus\Message\MessageHandler;
use ShopBundle\Entity\BannerMainTemplate;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetTemplateQueryHandler extends MessageHandler
{
    public function handle(GetTemplateQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $model = $em->getRepository(BannerMainTemplate::class)->find($query->id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}