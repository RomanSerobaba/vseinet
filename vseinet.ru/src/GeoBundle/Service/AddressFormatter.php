<?php 

namespace GeoBundle\Service;

use AppBundle\Container\ContainerAware;

class AddressFormatter extends ContainerAware
{
    public function format(int $id): DTO\Address 
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT 
                NEW GeoBundle\Service\DTO\Address (
                    ga.id,
                    gs.name,
                    gs.unit,
                    ga.house,
                    ga.building,
                    ga.apartment
                )
            FROM GeoBundle:GeoAddress AS ga 
            INNER JOIN GeoBundle:GeoStreet AS gs WITH gs.id = ga.geoStreetId
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
