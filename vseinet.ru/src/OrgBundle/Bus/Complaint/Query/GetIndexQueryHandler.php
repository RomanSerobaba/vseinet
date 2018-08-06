<?php 

namespace OrgBundle\Bus\Complaint\Query;

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
                NEW OrgBundle\Bus\Complaint\Query\DTO\Complaint (
                    c.id,
                    c.text,
                    c.createdAt,
                    c.createdBy,
                    c.firstname,
                    c.phone,
                    c.email,
                    c.isChecked,
                    c.manager,
                    c.managerPhone,
                    c.type
                )
            FROM
                OrgBundle:Complaint AS c
            WHERE 1 = 1'
                .(!$query->isAll ? ' AND c.isChecked = FALSE' : '')
                .(!empty($query->lastId) ? ' AND c.id < :id' : '')
                .' ORDER BY c.id DESC';

        $q = $em->createQuery($sql);
        $q->setMaxResults($query->limit);
        if (!empty($query->lastId))
            $q->setParameter('id', $query->lastId, Type::INTEGER);

        $complaints = $q->getResult();

        $ids = array_map(function ($c) { return $c->id; }, $complaints);

        if ($ids) {
            $sql = '
                SELECT
                    cc.*,
                    vup.fullname
                FROM
                    complaint_comment AS cc
                    INNER JOIN func_view_user_person(cc.created_by) vup ON vup.user_id = cc.created_by
                WHERE
                    cc.complaint_id IN (:ids) AND cc.text <> \'\'
                ORDER BY
                    cc.created_at
            ';
            $q = $em->createNativeQuery($sql, new DTORSM(\OrgBundle\Bus\Complaint\Query\DTO\ComplaintComment::class));
            $q->setParameter('ids', $ids);

            $complaintComment = $q->getResult('DTOHydrator');

            $ids = array_flip($ids);
            foreach ($complaintComment as $comment) {
                $complaints[$ids[$comment->complaintId]]->comments[] = $comment;
            }
        }

        return $complaints;
    }
}