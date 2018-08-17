<?php 

namespace AppBundle\Service;

use AppBundle\Container\ContainerAware;

class AddressFormatter extends ContainerAware
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor
     *
     * @param EntityManager $em;
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function format(int $id): DTO\Address 
    {
        $q = $this->em->createQuery("
            SELECT 
                NEW AppBundle\Service\DTO\Address (
                    ga.id,
                    gs.name,
                    gs.unit,
                    ga.house,
                    ga.building,
                    ga.apartment
                )
            FROM AppBundle:GeoAddress AS ga 
            INNER JOIN AppBundle:GeoStreet AS gs WITH gs.id = ga.geoStreetId
            WHERE ga.id = :id 
        ");
        $q->setParameter('id', $id);
        try {
            $address = $q->getSingleResult();
        } catch (\Exception $e) {
            $address = new DTO\Address();
        }

        return $address->format();
    }
}
