<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;
use ContentBundle\Entity\CategorySection;
use ContentBundle\Entity\BaseProductEditLog;
use AppBundle\Enum\BaseProductEditTarget;

class SetCategorySectionCommandHandler extends MessageHandler
{
    public function handle(SetCategorySectionCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $command->id));
        }

        if (0 === $command->sectionId) {
            $section = new CategorySection();
        } else {
            $section = $em->getRepository(CategorySection::class)->find($command->sectionId);
            if (!$section instanceof CategorySection) {
                throw new NotFoundHttpException(sprintf('Раздел категории %d не найден', $command->sectionId));
            }
        }

        // @todo: добавить в логи изменение раздела категории
        // $em->getRepository(BaseProductEditLog::class)->add(
        //     $product,
        //     BaseProductEditTarget::SECTION,
        //     null, 
        //     $this->get('user.identity')->getUser(),
        //     $product->getSectionId(),
        //     $section->getId()
        // );

        $product->setSectionId($section->getId());

        $em->persist($product);
        $em->flush();
    }
}