<?php

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetServicesQueryHandler extends MessageHandler
{
    public function handle(GetServicesQuery $query)
    {
        $cache = $this->get('cache.provider.memcached');
        $cachedServices = $cache->getItem('services');
        if ($cachedServices->isHit()) {
            return $cachedServices->get();
        }

        $result = ['services' => []];

        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT new AppBundle\Bus\Main\Query\DTO\Service(
                p.id,
                p.url,
                p.slug,
                p.titleShort
            )
            FROM AppBundle:ContentPage AS p
            WHERE
                p.isActive = true
                AND p.slug = 'service'
        ");
        $services = $q->getResult();
        if (!empty($services)) {
            $result['services'] = $services;
        }

        $cachedServices->set($result);
        $cachedServices->expiresAfter(300 + rand(0, 100));
        $cache->save($cachedServices);

        return $result;
    }
}
