<?php

namespace AppBundle\Repository;

/**
 * CompetitorToGeoCityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompetitorToGeoCityRepository extends \Doctrine\ORM\EntityRepository
{
    public function getActive()
    {
        return $this->findBy(['isActive' => true]);
    }
}