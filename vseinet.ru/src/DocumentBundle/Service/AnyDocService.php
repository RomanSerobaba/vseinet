<?php

namespace DocumentBundle\Service;

use DocumentBundle\Bus\Inventory\Command;
use AppBundle\Container\ContainerAware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
    
class AnyDocService extends ContainerAware
{
    
}
