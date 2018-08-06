<?php 

namespace ContentBundle\Bus\Statistics\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Manager;
use ContentBundle\Entity\Category;
use ContentBundle\Entity\BaseProduct;

class GetFulfillmentStatsQueryHandler extends MessageHandler
{
    public function handle(GetFulfillmentStatsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $criteria = [
            "fl.createdAt BETWEEN :fromDate AND :toDate",
        ];
        $parameters = [
            'fromDate' => date('Y-m-d 00:00:00', strtotime($query->fromDate ? $query->fromDate->format('Y-m-d') : date('Y-m-1'))),
            'toDate' => date('Y-m-d 23:59:59', strtotime($query->toDate ? $query->toDate->format('Y-m-d') : date('Y-m-d'))),
        ];
        if ($query->status) {
            $criteria[] = "fl.status = :status";
            $parameters['status'] = $query->status;
        }

        switch ($query->fillType) {
            case 'hasnt-images':
                $criteria[] = "
                    NOT EXISTS (
                        SELECT 1 
                        FROM ContentBundle:BaseProductImage bpi WHERE bpi.baseProductId = bp.id AND bpi.sortOrder = 1
                    )
                ";
                break;

            case 'nasnt-barnd':
                $criteria[] = "bp.brandId IS NULL";
                break;

            case 'hasnt-color':
                $criteria[] = "bp.colorCompositeId IS NULL";
                break;

            case 'hasnt-model':
                $criteria[] = "
                    NOT EXISTS (
                        SELECT 1 
                        FROM ContentBundle:BaseProductData bpd WHERE bpd.baseProductId = bp.id AND bpd.model IS NULL 
                    )
                ";
                break;

            case 'hasnt-description':
                $criteria[] = "
                    NOT EXISTS (
                        SELECT 1 
                        FROM ContentBundle:BaseProductDescription bpd WHERE bpd.baseProductId = bp.id 
                    )
                ";
                break;

            case 'hasnt-manual-link':
                $criteria[] = "
                    NOT EXISTS (
                        SELECT 1 
                        FROM ContentBundle:BaseProductData bpd WHERE bpd.baseProductId = bp.id AND bpd.manualLink IS NULL 
                    )
                ";
                break;

            case 'hasnt-manufacturer-link':
                $criteria[] = "
                    NOT EXISTS (
                        SELECT 1 
                        FROM ContentBundle:BaseProductData bpd WHERE bpd.baseProductId = bp.id AND bpd.manufacturerLink IS NULL 
                    )
                ";
                break;
        }

        if ($query->managerId) {
            $manager = $em->getRepository(Manager::class)->find($query->managerId);
            if (!$manager instanceof Manager) {
                throw new NotFoundHttpException(sprintf('Менеджер %d не найден', $query->managerId));
            }
            $criteria[] = "fl.managerId = :managerId";
            $parameters['managerId'] = $manager->getUserId();
        }

        if ($query->categoryId) {
            $category = $em->getRepository(Category::class)->find($query->categoryId);
            if (!$category instanceof Category) {
                throw new NotFoundHttpException(sprintf('Категория %s не найдена', $query->categoryId));
            }
            $criteria[] = "bp.categoryId = :categoryId";
            $parameters['categoryId'] = $category->getId();
        }

        if ($query->baseProductId) {
            $baseProduct = $em->getRepository(BaseProduct::class)->find($query->baseProductId);
            if (!$baseProduct instanceof BaseProduct) {
                throw new NotFoundHttpException(sprintf('Товар %d не найден', $query->baseProductId));
            }
            $criteria[] = "fl.baseProductId = :baseProductId";
            $parameters['baseProductId'] = $baseProduct->getId();
        }

        $where = "WHERE ".implode(" AND ", $criteria); 

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Statistics\Query\DTO\FulfillmentStats (
                    COUNT(fl.id)
                )
            FROM ContentBundle:FulfillmentLog fl 
            INNER JOIN ContentBundle:BaseProduct bp WITH bp.id = fl.baseProductId 
            {$where}
        ");
        $q->setParameters($parameters);
        $stats = $q->getOneOrNullResult();
        if (!$stats || !$stats->count) {
            return $stats;
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Statistics\Query\DTO\FulfillmentLog (
                    fl.id,
                    fl.createdAt,
                    TRIM(CONCAT(p.lastname, ' ', p.firstname, ' ', p.secondname)),
                    bp.id,
                    bp.name,
                    fl.status,
                    fl.cost
                )
            FROM ContentBundle:FulfillmentLog fl  
            INNER JOIN ContentBundle:BaseProduct bp WITH bp.id = fl.baseProductId 
            INNER JOIN AppBundle:User u WITH u.id = fl.managerId 
            INNER JOIN AppBundle:Person p WITH p.id = u.personId
            {$where}
            ORDER BY fl.createdAt
        ");
        $q->setParameters($parameters);
        $q->setFirstResult($query->limit * ($query->page - 1));
        $q->setMaxResults($query->limit);
        $stats->logs = $q->getArrayResult();

        return $stats;
    }
}