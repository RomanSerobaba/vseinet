<?php 

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\BaseProductLastview;

class AddLastviewProductCommandHandler extends MessageHandler
{
    const LIMIT = 6;

    public function handle(AddLastviewProductCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->baseProductId);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException();
        }

        if (null !== $user) {
            $q = $em->createQuery("
                SELECT bplv.baseProductId, bplv.baseProductId
                FROM AppBundle:BaseProductLastview AS bplv
                WHERE bplv.userId = :userId 
                ORDER BY bplv.viewedAt DESC 
            ");
            $q->setParameter('userId', $user->getId());
            $baseProductIds = $q->getResult('ListHydrator');
            if (!empty($baseProductIds)) {
                if (isset($baseProductIds[$command->baseProductId])) {
                    $q = $em->createQuery("
                        DELETE FROM AppBundle:BaseProductLastview AS bplv
                        WHERE bplv.baseProductId = :baseProductId
                    ");
                    $q->setParameter('baseProductId', $command->baseProductId);
                    $q->execute();
                    unset($baseProductIds[$command->baseProductId]);
                }
                if (self::LIMIT < count($baseProductIds)) {
                    $deleteIds = array_slice($baseProductIds, self::LIMIT);
                    $q = $em->createQuery("
                        DELETE FROM AppBundle:BaseProductLastview AS bplv 
                        WHERE bplv.baseProductId IN (:baseProductIds) AND bplv.userId = :userId
                    ");
                    $q->setParameter('baseProductIds', $deleteIds);
                    $q->setParameter('userId', $user->getId());
                    $q->execute();
                }
            }
            $product = new BaseProductLastview();
            $product->setUserId($user->getId());
            $product->setBaseProductId($command->baseProductId);
            $product->setViewedAt(new \DateTime());
            $em->persist($product);
            $em->flush($product);
        } else {
            $request = $this->get('request_stack')->getMasterRequest();
            $baseProductIds = [];
            $baseProductIdsStr = $request->cookies->get('products_lastview', '');
            if (!empty($baseProductIdsStr)) {
                $baseProductIds = array_filter(array_map('intval', explode(',', $baseProductIdsStr)));
            }
            if (!empty($baseProductIds)) {
                unset($baseProductIds[$command->baseProductId]);
                if (self::LIMIT < count($baseProductIds)) {
                    $baseProductIds = array_slice($baseProductIds, count($baseProductIds) - self::LIMIT);
                }
            }
            $baseProductIds[] = $command->baseProductId;
            $request->cookies->set('products_lastview', implode(',', $baseProductIds));
        }
    }
}
