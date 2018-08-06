<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;
use ContentBundle\Entity\BaseProductData;
use ContentBundle\Entity\BaseProductDescription;
use ContentBundle\Entity\BaseProductEditLog;
use ContentBundle\Entity\Manager;
use ContentBundle\Entity\Category;
use AppBundle\Enum\BaseProductEditTarget;
use AppBundle\Enum\CategoryTpl;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $command->id));
        }

        $data = $em->getRepository(BaseProductData::class)->find(['baseProductId' => $command->id]);
        if (!$data instanceof BaseProductData) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $command->id));
        }

        $category = $em->getRepository(Category::class)->find($product->getCategoryId());
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена'), $product->getCategoryId());
        }

        if ($data->getModel() != $command->model) {
            $this->addLog($product, BaseProductEditTarget::MODEL, $data->getModel(), $command->model);
            $data->setModel($command->model);
        }

        if ($data->getManufacturerLink() != $command->manufacturerLink) {
            $this->addLog($product, BaseProductEditTarget::MANUFACTURER_LINK, $data->getManufacturerLink(), $command->manufacturerLink);
            $data->setManufacturerLink($command->manufacturerLink);
        }

        if ($data->getManualLink() != $command->manualLink) {
            $this->addLog($product, BaseProductEditTarget::MANUAL_LINK, $data->getManualLink(), $command->manualLink);
            $data->setManualLink($command->manualLink);
        }

        $em->merge($data);

        $description = $em->getRepository(BaseProductDescription::class)->find(['baseProductId' => $command->id]);
        if ($command->description) {
            if (!$description instanceof BaseProductDescription) {
                $description = new BaseProductDescription();
                $description->setBaseProductId($product->getId());
            }
            if ($description->getDescription() != $command->description) {
                $this->addLog($product, BaseProductEditTarget::DESCRIPTION, $description->getDescription(), $command->description);
                $description->setDescription($command->description);
                $em->persist($description);
            }
        }
        else {
            if ($description instanceof BaseProductDescription) {
                $this->addLog($product, BaseProductEditTarget::DESCRIPTION, $description->getDescription(), null);
                $em->remove($description);
            }
        }

        $em->flush();
    }

    protected function addLog(BaseProduct $product, $target, $oldValue, $newValue)
    {
        $this->getDoctrine()->getManager()->getRepository(BaseProductEditLog::class)->add(
            $product, 
            $target, 
            null,
            $this->get('user.identity')->getUser(), 
            $oldValue, 
            $newValue
        );
    }
}
