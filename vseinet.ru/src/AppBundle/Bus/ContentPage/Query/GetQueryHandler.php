<?php 

namespace AppBundle\Bus\ContentPage\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\ContentPage;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $page = $em->getRepository(ContentPage::class)->findOneBy(['slug' => $query->slug, 'isActive' => true]);
        if (!$page instanceof ContentPage) {
            throw new NotFoundHttpException(sprintf('Страница "%s" не найдена', $query->slug));
        }

        return $page;
    }
}