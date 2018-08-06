<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;
use ContentBundle\Entity\Detail;
use ContentBundle\Entity\DetailToProduct;
use ContentBundle\Entity\DetailMemoToProduct;
use ContentBundle\Entity\BaseProductEditLog;
use ContentBundle\Entity\Manager;
use AppBundle\Enum\BaseProductEditTarget;
use AppBundle\Enum\DetailType;

class DeleteDetailValueCommandHandler extends MessageHandler
{
    public function handle(DeleteDetailValueCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $command->id));
        }

        $detail = $em->getRepository(Detail::class)->find($command->detailId);
        if (!$detail instanceof Detail) {
            throw new NotFoundHttpException(sprintf('Характеристика %d не найдена', $command->detailId));
        }

        $em->getRepository(Manager::class)->check($this->get('user.identity')->getUser());

        if (DetailType::CODE_MEMO == $detail->getTypeCode()) {
            $detailMemoToProduct = $em->getRepository(DetailMemoToProduct::class)->findOneBy([
                'baseProductId' => $product->getId(),
                'detailId' => $detail->getId(),
            ]);
            if (!$detailMemoToProduct instanceof DetailMemoToProduct) {
                throw new NotFoundHttpException(sprintf('Значение характеристики %d не найдено', $detail->getId()));
            }    
            $this->addLog($product, $detail, $detailMemoToProduct->getMemo());
            $em->remove($detailMemoToProduct);
        } else {
            $detailToProduct = $em->getRepository(DetailToProduct::class)->findOneBy([
                'baseProductId' => $product->getId(),
                'detailId' => $detail->getId(),
            ]);
            if (!$detailToProduct instanceof DetailToProduct) {
                throw new NotFoundHttpException(sprintf('Значение характеристики %d не найдено', $detail->getId()));   
            }
            if (DetailType::CODE_STRING == $detail->getTypeCode() || DetailType::CODE_ENUM == $detail->getTypeCode()) {
                $this->addLog($product, $detail, $detailToProduct->getValueId());
            }
            else {
                $this->addLog($product, $detail, $detailToProduct->getValue());
            }
            $em->remove($detailToProduct);
        }

        $em->flush();
    }

    protected function addLog(BaseProduct $product, Detail $detail, $oldValue)
    {
        $this->getDoctrine()->getManager()->getRepository(BaseProductEditLog::class)->add(
            $product, 
            BaseProductEditTarget::DETAIL, 
            $detail->getId(),
            $this->get('user.identity')->getUser(), 
            $oldValue, 
            null
        );
    }
}