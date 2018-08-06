<?php 

namespace SiteBundle\Bus\Main\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;
use SiteBundle\Entity\BaseProductLastview;

class AddLastviewProductCommandHandler extends MessageHandler
{
    const LIMIT = 6;

    public function handle(AddLastviewProductCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($command->baseProductId);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException();
        }

        if ($this->get('user.identity')->isAuthorized()) {
            $user = $this->get('user.identity')->getUser();

            $q = $em->createQuery("
                SELECT bplv.baseProductId, bplv.baseProductId
                FROM SiteBundle:baseProductLastview AS bplv
                WHERE bplv.useId = :userId 
                ORDER BY bplv.viewedAt DESC 
            ");
            $q->setParameter('userId', $user->getId());
            $productIds = $q->getArrayResult('ListHydrator');

            if (!empty($productIds)) {
                unset($productIds[$command->baseProductId]);
                if (self::LIMIT < count($productIds)) {
                    $deleteIds = array_slice($productIds, self::LIMIT);
                    $q = $em->createQuery("
                        DELETE FROM SiteBundle:baseProductLastview AS bplv 
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
            $productIdsStr = $request->cookies->get('products_lastview', '');
            $productIds = empty($productIdsStr) ? [] : array_filter(array_map('intval', explode(',', $productIdsStr)));
            if (!empty($productIds)) {
                unset($productIds[$command->baseProductId]);
                if (self::LIMIT < count($productIds)) {
                    $productIds = array_slice($productIds, count($productIds) - self::LIMIT);
                }
            }
            $productIds[] = $command->baseProductId;
            $request->cookies->set('products_lastview', implode(',', $productIds));
        }
    }
}
