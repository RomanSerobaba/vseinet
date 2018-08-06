<?php 

namespace PromoBundle\Bus\ProductReview\Query;

use AppBundle\ORM\Query\DTORSM;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\Bus\Message\MessageHandler;
use OrgBundle\Bus\Complaint\Query\DTO\Complaint;
use OrgBundle\Bus\Complaint\Query\DTO\ComplaintComment;

class GetIndexQueryHandler extends MessageHandler
{
    public function handle(GetIndexQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $sql = '
            SELECT
                product_review.id,
                product_review.created_by,
                product_review.created_at,
                product_review.comment,
                product_review.advantages,
                product_review.disadvantages,
                product_review.estimate,
                product_review.base_product_id,
                product_review.approved_at,
                product_review.approved_by,
                product_review.deleted_by,
                CASE WHEN vup.id IS NOT NULL 
                    THEN CONCAT(vup.email, \' \', vup.phone, \' \', vup.mobile)
                    ELSE product_review.contacts
                END AS contacts,
                CASE WHEN vup.id IS NOT NULL 
                    THEN vup.fullname
                    ELSE product_review.name
                END AS name,
                product_review.answer
            FROM
                product_review
                LEFT JOIN func_view_user_person(product_review.created_by) vup ON vup.user_id = product_review.created_by
            WHERE
                deleted_at IS NULL '
                .(!$query->isAll ? ' AND product_review.approved_at IS NULL' : '')
                .(!empty($query->lastId) ? ' AND product_review.id > :id' : '')
                .' ORDER BY id LIMIT :limit';

        $q = $em->createNativeQuery($sql, new DTORSM(\PromoBundle\Bus\ProductReview\Query\DTO\ProductReview::class));
        $q->setParameter('limit', $query->limit, Type::INTEGER);
        $q->setParameter('id', $query->lastId, Type::INTEGER);

        return $q->getResult('DTOHydrator');
    }
}