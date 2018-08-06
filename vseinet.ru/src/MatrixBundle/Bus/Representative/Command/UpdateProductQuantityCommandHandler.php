<?php 

namespace MatrixBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MatrixBundle\Entity\TradeMatrixTemplate;
use OrgBundle\Entity\Representative;
use MatrixBundle\Entity\TradeMatrixProductToRepresentative;
use MatrixBundle\Entity\TradeMatrixTemplateProduct;
use ContentBundle\Entity\BaseProduct;

class UpdateProductQuantityCommandHandler extends MessageHandler
{
    public function handle(UpdateProductQuantityCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $representative = $em->getRepository(Representative::class)->find($command->id);
        if (!$representative instanceof Representative) {
            throw new NotFoundHttpException(sprintf('Точка %d не найдена', $command->id));
        }

        if ($command->templateId > 0) {
            $template = $em->getRepository(TradeMatrixTemplate::class)->find($command->templateId);
            if (!$template instanceof TradeMatrixTemplate) {
                throw new NotFoundHttpException(sprintf('Шаблон %d не найден', $command->templateId));
            }

            $templateProduct = $em->getRepository(TradeMatrixTemplateProduct::class)->findOneBy(['tradeMatrixTemplateId' => $command->templateId, 'baseProductId' => $command->baseProductId]);
            if (!$templateProduct instanceof TradeMatrixTemplateProduct) {
                throw new NotFoundHttpException(sprintf('Товар %d отстутствует в шаблоне %d', $command->baseProductId, $command->templateId));
            }
        }

        $product = $em->getRepository(BaseProduct::class)->find($command->baseProductId);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $command->baseProductId));
        }

        $representativeProduct = $em->getRepository(TradeMatrixProductToRepresentative::class)->findOneBy(['representativeId' => $command->id, 'tradeMatrixTemplateId' => $command->templateId, 'baseProductId' => $command->baseProductId]);

        if (!$representativeProduct instanceof TradeMatrixProductToRepresentative) {
            $representativeProduct = new TradeMatrixProductToRepresentative();
            $representativeProduct->setRepresentativeId($command->id);
            $representativeProduct->setTradeMatrixTemplateId($command->templateId);
            $representativeProduct->setBaseProductId($command->baseProductId);
        }

        $representativeProduct->setQuantity($command->quantity);
        $em->persist($representativeProduct);
    }
}