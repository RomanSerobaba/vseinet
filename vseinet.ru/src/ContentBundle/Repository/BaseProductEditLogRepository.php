<?php

namespace ContentBundle\Repository;

use ContentBundle\Entity\BaseProduct;
use ContentBundle\Entity\BaseProductEditLog;
use ContentBundle\Entity\Detail;
use AppBundle\Enum\BaseProductEditTarget;
use AppBundle\Entity\User;

/**
 * BaseProductEditLogRepository
 */
class BaseProductEditLogRepository extends \Doctrine\ORM\EntityRepository
{
    public function add(BaseProduct $product, $target, $targetId, User $manager, $oldValue, $newValue)
    {
        $reflection = new \ReflectionClass(BaseProductEditTarget::class);
        if (!in_array($target, $reflection->getConstants())) {
            throw new \RuntimeException(sprintf('Unknown edit target `%s`', $target));
        }

        $log = new BaseProductEditLog();
        $log->setBaseProductId($product->getId());
        $log->setManagerId($manager->getId());
        $log->setTarget($target);
        $log->setTargetId($targetId);
        $log->setOldValue($oldValue);
        $log->setNewValue($newValue);
        $log->setChangedAt(new \DateTime());

        $this->getEntityManager()->persist($log);
    }
}
