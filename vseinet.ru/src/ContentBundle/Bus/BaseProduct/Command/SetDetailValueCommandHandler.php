<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;
use ContentBundle\Entity\Detail;
use ContentBundle\Entity\DetailValue;
use ContentBundle\Entity\DetailToProduct;
use ContentBundle\Entity\DetailMemoToProduct;
use ContentBundle\Entity\BaseProductEditLog;
use ContentBundle\Entity\Manager;
use AppBundle\Enum\BaseProductEditTarget;
use AppBundle\Enum\DetailType;

class SetDetailValueCommandHandler extends MessageHandler
{
    public function handle(SetDetailValueCommand $command)
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
                $detailMemoToProduct = new DetailMemoToProduct();  
                $detailMemoToProduct->setBaseProductId($product->getId());
                $detailMemoToProduct->setDetailId($detail->getId());
                $this->addLog($product, $detail, null, $command->value);  
            } elseif ($detailMemoToProduct->getMemo() != $command->value) {
                $this->addLog($product, $detail, $detailMemoToProduct->getMemo(), $command->value);
            }
            $detailMemoToProduct->setMemo($command->value);
            $em->persist($detailMemoToProduct);
        } else {
            $detailToProduct = $em->getRepository(DetailToProduct::class)->findOneBy([
                'baseProductId' => $product->getId(),
                'detailId' => $detail->getId(),
            ]);
            if (DetailType::CODE_STRING == $detail->getTypeCode() || DetailType::CODE_ENUM == $detail->getTypeCode()) {
                $value = $em->getRepository(DetailValue::class)->find($command->value);
                if (!$value instanceof DetailValue) {
                    throw new NotFoundHttpException(sprintf('Значение строковой характеристики %d не найдено', $command->value));
                }
                if (!$detailToProduct instanceof DetailToProduct) {
                    $detailToProduct = new DetailToProduct();
                    $detailToProduct->setBaseProductId($product->getId());
                    $detailToProduct->setDetailId($detail->getId());
                    $this->addLog($product, $detail, null, $command->value);
                } elseif ($detailToProduct->getValueId() != $command->value) {
                    $this->addLog($product, $detail, $detailToProduct->getValueId(), $command->value);
                }   
                $detailToProduct->setValueId($command->value);
                $em->persist($detailToProduct);
            } else {
                if (!$detailToProduct instanceof DetailToProduct) {
                    $detailToProduct = new DetailToProduct();
                    $detailToProduct->setBaseProductId($product->getId());
                    $detailToProduct->setDetailId($detail->getId());
                    $this->addLog($product, $detail, null, $command->value);
                } elseif ($detailToProduct->getValue() != $command->value) {
                    $this->addLog($product, $detail, $detailToProduct->getValue(), $command->value);
                }  
                if (DetailType::CODE_BOOLEAN == $detail->getTypeCode()) {
                    $command->value = filter_var($command->value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                } 
                $detailToProduct->setValue($command->value);
                $em->persist($detailToProduct);
            }
        }

        $em->flush();
    }

    protected function addLog(BaseProduct $product, Detail $detail, $oldValue, $newValue)
    {
        $this->getDoctrine()->getManager()->getRepository(BaseProductEditLog::class)->add(
            $product, 
            BaseProductEditTarget::DETAIL, 
            $detail->getId(),
            $this->get('user.identity')->getUser(), 
            $oldValue, 
            $newValue
        );
    }
}