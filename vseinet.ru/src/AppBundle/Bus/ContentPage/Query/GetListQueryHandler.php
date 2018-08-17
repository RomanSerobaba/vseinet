<?php 

namespace AppBundle\Bus\ContentPage\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\ContentPage;
use AppBundle\Entity\ContentPageCategory;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(ContentPageType::class)->findOneBy(['type' => $query->type]);
        if (!$category instanceof ContentPageCategory) {
            throw new NotFoundHttpException(sprintf('Раздел "%s" не найден', $query->type));
        }

        $pages = $em->getRepository(ContentPage::class)->findBy([
            'categoryId' => $category->getId(),
            'isActive' => true,
        ], ['sortOrder' => 'ASC']); 

        return [
            'category' => $category,
            'pages' => $pages,
        ];
    }
}