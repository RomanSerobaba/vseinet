<?php 

namespace ContentBundle\Bus\Brand\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetAlphabetIndexQueryHandler extends MessageHandler
{
    public function handle(GetAlphabetIndexQuery $query) 
    {
        switch ($query->filter) {
            case 'all':
                $where = "";
                break;

            case 'active':
                $where = "WHERE b.isForbidden = false";
                break;

            case 'forbidden':
                $where = "WHERE b.isForbidden = true";
                break;
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Brand\Query\DTO\AlphabetIndex (
                    UPPER(SUBSTRING(TRIM(b.name), 1, 1)),
                    COUNT(bp.id) 
                ),
                UPPER(SUBSTRING(TRIM(b.name), 1, 1)) HIDDEN firstLetter
            FROM ContentBundle:Brand b 
            LEFT OUTER JOIN ContentBundle:BaseProduct bp WITH bp.brandId = b.id 
            {$where}
            GROUP BY firstLetter
            ORDER BY firstLetter
        ");

        return $q->getArrayResult(); 
    }
}