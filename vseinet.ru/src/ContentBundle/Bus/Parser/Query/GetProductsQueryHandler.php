<?php 

namespace ContentBundle\Bus\Parser\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ParserSource;

/**
 * @deprecated
 */
class GetProductsQueryHandler extends MessageHandler
{
    public function handle(GetProductsQuery $query)
    {
        if (null === $query->fromDate) {
            $query->fromDate = new \DateTime('Y-m-01');
        }
        if (null === $query->toDate) {
            $query->toDate = new \DateTime('Y-m-d');
        }

        $criteria = [
            ''
        ];


        $criteria = [];
        if ($query->filter == 'active') {
            $criteria['isActive'] = true;   
        }

        return $this->getDoctrine()->getManager()->getRepository(ParserSource::class)->findBy($criteria, ['sortOrder' => 'ASC']);
    }
}