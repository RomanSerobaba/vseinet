<?php 

namespace MatrixBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use OrgBundle\Entity\Representative;
use MatrixBundle\Entity\TradeMatrixCategoryToRepresentative;
use MatrixBundle\Entity\TradeMatrixProductToRepresentative;
use Doctrine\ORM\Query\ResultSetMapping;

class CopyMatrixCommandHandler extends MessageHandler
{
    public function handle(CopyMatrixCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $representative = $em->getRepository(Representative::class)->find($command->id);
        if (!$representative instanceof Representative) {
            throw new NotFoundHttpException(sprintf('Точка %d не найдена', $command->id));
        }

        $representative = $em->getRepository(Representative::class)->find($command->sourceRepresentativeId);
        if (!$representative instanceof Representative) {
            throw new NotFoundHttpException(sprintf('Точка-донор %d не найдена', $command->sourceRepresentativeId));
        }

        $query = $em->createNativeQuery("
            DELETE FROM trade_matrix_category_to_representative WHERE representative_id = :representativeId
        ", new ResultSetMapping());
        $query->setParameter('representativeId', $command->id);
        $query->execute();

        $query = $em->createNativeQuery("
            DELETE FROM trade_matrix_product_to_representative WHERE representative_id = :representativeId AND trade_matrix_template_id IS NULL
        ", new ResultSetMapping());
        $query->setParameter('representativeId', $command->id);
        $query->execute();
        
        $user = $this->get('user.identity')->getUser();

        $query = $em->createNativeQuery("
            INSERT INTO 
                trade_matrix_category_to_representative
            (
                representative_id, 
                category_id, 
                quantity, 
                updated_at, 
                updated_by
            )
            SELECT
                :representativeId, 
                category_id, 
                quantity, 
                NOW(), 
                :userId
            FROM trade_matrix_category_to_representative
            WHERE 
                representative_id = :sourceRepresentativeId
        ", new ResultSetMapping());
        $query->setParameter('representativeId', $command->id);
        $query->setParameter('sourceRepresentativeId', $command->sourceRepresentativeId);
        $query->setParameter('userId', $user->getId());
        $query->execute();

        $query = $em->createNativeQuery("
            INSERT INTO 
                trade_matrix_product_to_representative
            (
                representative_id, 
                base_product_id, 
                trade_matrix_template_id,
                quantity, 
                updated_at, 
                updated_by
            )
            SELECT
                :representativeId, 
                base_product_id, 
                NULL,
                quantity, 
                NOW(), 
                :userId
            FROM trade_matrix_product_to_representative
            WHERE 
                representative_id = :sourceRepresentativeId AND trade_matrix_template_id IS NULL
        ", new ResultSetMapping());
        $query->setParameter('representativeId', $command->id);
        $query->setParameter('sourceRepresentativeId', $command->sourceRepresentativeId);
        $query->setParameter('userId', $user->getId());
        $query->execute();
    }
}