<?php 

namespace OrgBundle\Bus\Representative\Query;

use Doctrine\ORM\Query\ResultSetMapping;
use OrgBundle\Entity\Representative;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Message\MessageHandler;

class GetRepresentativeQueryHandler extends MessageHandler
{
    public function handle(GetRepresentativeQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $data = [];
        $representativeModel = $em->getRepository(Representative::class)->findOneBy(['geo_point_id' => $query->id,]);

        if (!$representativeModel) {
            throw new NotFoundHttpException('Представление не найдено');
        }

        $data['geo_point_id'] = $representativeModel->getGeoPointId();
        $data['has_warehouse'] = $representativeModel->getHasWarehouse();
        $data['has_retail'] = $representativeModel->getHasRetail();
        $data['has_order_issueing'] = $representativeModel->getHasOrderIssueing();
        $data['has_delivery'] = $representativeModel->getHasDelivery();
        $data['has_rising'] = $representativeModel->getHasRising();
        $data['is_active'] = $representativeModel->getIsActive();
        $data['type'] = $representativeModel->getType();
        $data['is_central'] = $representativeModel->getIsCentral();
        $data['ip'] = $representativeModel->getIp();
        $data['delivery_tax'] = $representativeModel->getDeliveryTax();

        // Rooms
        if ($representativeModel->getGeoPointId()) {
            $sql = '
                SELECT
                    gp.name point,
                    gp.code point_code,
                    gp.geo_address_id,
                    gc.name city,
                    gc.id city_id,
                    gr.id room_id,
                    gr.name room_name,
                    gr.code room_code,
                    gr.type room_type,
                    gr.is_default,
                    gr.has_auto_release,
                    gr.write_off_order
                FROM
                    geo_point gp
                    INNER JOIN geo_city gc ON gc.id = gp.geo_city_id
                    LEFT JOIN geo_room gr ON gr.geo_point_id = gp.id 
                WHERE
                    gp.id = :geo_point_id
            ';

            $q = $em->createNativeQuery($sql, new ResultSetMapping());
            $q->setParameter('geo_point_id', $representativeModel->getGeoPointId());

            $data['rooms'] = $q->getResult('ListAssocHydrator');

            $room = !empty($data['rooms']) ? $data['rooms'][0] : [];

            if (!empty($room['geo_address_id'])) {
                // Address
                $sql = '
                    SELECT
                        id,
                        coordinates,
                        "comment" AS address
                    FROM
                        geo_address 
                    WHERE
                        id = :geo_address_id
                ';

                $q = $em->createNativeQuery($sql, new ResultSetMapping());
                $q->setParameter('geo_address_id', $room['geo_address_id']);

                $rows = $q->getResult('ListAssocHydrator');
                $data['address'] = array_shift($rows);
            }
        }

        // Managers
        $sql = '
            SELECT
                vup.user_id,
                vup.fullname
            FROM
                geo_point gp
                INNER JOIN user_to_address u2a ON gp.geo_address_id = u2a.geo_address_id
                INNER JOIN func_view_user_person(u2a.user_id) vup ON vup.user_id = u2a.user_id
            WHERE
                gp.id = :id
            ORDER BY
                vup.fullname
        ';

        $q = $em->createNativeQuery($sql, new ResultSetMapping());
        $q->setParameter('id', $query->id);

        $data['managers'] = $q->getResult('ListAssocHydrator');

        /// Images
        $photoSrc = DIRECTORY_SEPARATOR.'u'.DIRECTORY_SEPARATOR.'contacts'.DIRECTORY_SEPARATOR.$query->id.'_p.jpg';
        $photoPath = $this->getParameter('project.web.path').$photoSrc;
        if(!file_exists($photoPath)) {
            $photoSrc = '';
        }
        $data['photo'] = $photoSrc;

        $sql = '
            SELECT
                id,
                url,
                title 
            FROM
                representative_photo 
            WHERE
                representative_id = :id 
            ORDER BY
                sort_order ASC
        ';

        $q = $em->createNativeQuery($sql, new ResultSetMapping());
        $q->setParameter('id', $query->id);

        $data['photos'] = $q->getResult('ListAssocHydrator');

        // Phones
        $sql = '
            SELECT
                c.id,
                c.contact_type_code,
                c."value"
            FROM
                contact c
                INNER JOIN representative_phone rp ON c."id" = rp.contact_id
            WHERE
                rp.representative_id = :id 
        ';

        $q = $em->createNativeQuery($sql, new ResultSetMapping());
        $q->setParameter('id', $query->id);

        $data['phones'] = $q->getResult('ListAssocHydrator');

        // Schedules
        $sql = '
            SELECT
                *
            FROM
                representative_schedule
            WHERE
                representative_id = :id 
        ';

        $q = $em->createNativeQuery($sql, new ResultSetMapping());
        $q->setParameter('id', $query->id);

        $rows = $q->getResult('ListAssocHydrator');
        $data['schedule'] = array_shift($rows);

        return $this->camelizeKeys($data);
    }
}