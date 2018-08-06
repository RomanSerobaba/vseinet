<?php 

namespace ContentBundle\Bus\Brand\Query;

use AppBundle\Bus\Message\MessageHandler;
use ContentBundle\Entity\BrandPseudo;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        switch ($query->filter) {
            case 'all':
                $where = "";
                break;

            case 'active':
                $where = " AND b.isForbidden = false";
                break;

            case 'forbidden':
                $where = " AND b.isForbidden = true";
                break;
        }

        $q = $em->createQuery("
            SELECT
                NEW ContentBundle\Bus\Brand\Query\DTO\Brand (
                    b.id,
                    b.name,
                    b.logo,
                    b.url,
                    b.isForbidden,
                    COUNT(bp.id) 
                )
            FROM ContentBundle:Brand b 
            LEFT OUTER JOIN ContentBundle:BaseProduct bp WITH bp.brandId = b.id
            WHERE UPPER(SUBSTRING(TRIM(b.name), 1, 1)) = UPPER(:firstLetter) {$where}
            GROUP BY b.id
            ORDER BY b.name
        ");
        $q->setParameter('firstLetter', $query->firstLetter);
        
        $brands = $q->getResult('IndexByHydrator');
        if (empty($brands)) {
            return [];
        }

        $pseudos = $em->getRepository(BrandPseudo::class)->findBy(['brandId' => array_keys($brands)], ['name' => 'ASC']);
        foreach ($pseudos as $pseudo) {
            $brands[$pseudo->getBrandId()]->pseudos[] = $pseudo;
        }

        return array_values($brands);
    }
}