<?php 

namespace OrgBundle\Bus\Suggestions\Query;

use AppBundle\ORM\Query\DTORSM;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\Bus\Message\MessageHandler;
use OrgBundle\Bus\Suggestions\Query\DTO\Suggestion;
use OrgBundle\Bus\Suggestions\Query\DTO\SuggestionComment;

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
                COUNT(*) as cnt
            FROM
                client_suggestion
            WHERE
                1 =1 '.(!$query->isAll ? ' AND is_checked = FALSE' : '').'
        ';

        $q = $em->createNativeQuery($sql, new ResultSetMapping());

        $counts = $q->getResult('ListAssocHydrator');
        $count = array_shift($counts);

        $sql = '
            SELECT
                *
            FROM
                client_suggestion
            WHERE
                1 =1 '.(!$query->isAll ? ' AND is_checked = FALSE' : '').(!empty($query->lastId) ? ' AND id > :id' : '').' ORDER BY id LIMIT :limit';

        $q = $em->createNativeQuery($sql, new DTORSM(\OrgBundle\Bus\Suggestions\Query\DTO\Suggestion::class));
        $q->setParameter('limit', $query->limit, Type::INTEGER);
        $q->setParameter('id', $query->lastId, Type::INTEGER);

        $suggestions = $q->getResult('DTOHydrator');

        $ids = [];
        foreach ($suggestions as $suggestion) {
            $ids[] = $suggestion->id;
        }

        if ($ids) {
            $sql = '
                SELECT
                    cc.*,
                    vup.fullname
                FROM
                    client_suggestion_comment AS cc
                    INNER JOIN func_view_user_person(cc.created_by) vup ON vup.user_id = cc.created_by
                WHERE
                    cc.client_suggestion_id IN (:ids)
                ORDER BY
                    cc.created_at
            ';
            $q = $em->createNativeQuery($sql, new DTORSM(\OrgBundle\Bus\Suggestions\Query\DTO\SuggestionComment::class));
            $q->setParameter('ids', $ids);

            $suggestionComments = $q->getResult('DTOHydrator');

            /**
             * @var $suggestion Suggestion
             */
            foreach ($suggestions as &$suggestion) {
                /**
                 * @var $comment SuggestionComment
                 */
                foreach ($suggestionComments as $comment) {
                    if ($suggestion->id == $comment->clientSuggestionId) {
                        $suggestion->comments[] = $comment;
                    }
                }
            }
        }

        return ['items' => $suggestions, 'pageCount' => ceil($count['cnt'] / $query->limit),];
    }
}