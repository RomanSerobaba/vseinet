<?php 

namespace SiteBundle\Bus\Brand\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetByIdQueryHandler extends MessageHandler
{
    public function handle(GetByIdQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW SiteBundle\Bus\Brand\Query\DTO\Brand (
                    b.id,
                    b.name,
                    b.url,
                    b.isForbidden
                )
            FROM ContentBundle:Brand b 
            WHERE b.id = :id
        ");
        $q->setParameter('id', $query->id);
        $brand = $q->getOneOrNullResult();
        if (!$brand instanceof DTO\Brand || ($brand->isForbidden && !$this->get('user.identity')->isEmployee())) {
            throw new NotFoundHttpException();
        }

        return $brand;
    }
}
