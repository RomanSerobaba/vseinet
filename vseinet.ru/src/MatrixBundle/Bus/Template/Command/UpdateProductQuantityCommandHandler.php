<?php 

namespace MatrixBundle\Bus\Template\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MatrixBundle\Entity\TradeMatrixTemplate;
use MatrixBundle\Entity\TradeMatrixTemplateProduct;
use ContentBundle\Entity\BaseProduct;

class UpdateProductQuantityCommandHandler extends MessageHandler
{
    public function handle(UpdateProductQuantityCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $template = $em->getRepository(TradeMatrixTemplate::class)->find($command->id);
        if (!$template instanceof TradeMatrixTemplate) {
            throw new NotFoundHttpException(sprintf('Шаблон %d не найден', $command->id));
        }

        $product = $em->getRepository(BaseProduct::class)->find($command->baseProductId);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $command->baseProductId));
        }

        $templateProduct = $em->getRepository(TradeMatrixTemplateProduct::class)->findOneBy(['tradeMatrixTemplateId' => $command->id, 'baseProductId' => $command->baseProductId]);

        if (0 == $command->quantity) {
            if ($templateProduct instanceof TradeMatrixTemplateProduct) {
                $em->remove($templateProduct);
            }
        } else {
            if (!$templateProduct instanceof TradeMatrixTemplateProduct) {
                $templateProduct = new TradeMatrixTemplateProduct();
                $templateProduct->setTradeMatrixTemplateId($command->id);
                $templateProduct->setBaseProductId($command->baseProductId);
            }

            $templateProduct->setQuantity($command->quantity);
            $em->persist($templateProduct);
        }
    }
}