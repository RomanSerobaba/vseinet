<?php 

namespace ReservesBundle\Bus\Inventory\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Entity\Inventory;
use ServiceBundle\Components\Number;
use ServiceBundle\Components\Utils;
use ServiceBundle\Services\PdfService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PrintCommandHandler extends MessageHandler
{
    public function handle(PrintCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $inventory = $em->getRepository(Inventory::class)->find($command->id);
        if (!$inventory instanceof Inventory) {
            throw new NotFoundHttpException();
        }

        $q = $em->createNativeQuery('
            SELECT
                *
            FROM
                view_geo_room 
            WHERE id = :id', new ResultSetMapping());
        $q->setParameter('id', $inventory->getGeoRoomId());

        $rows = $q->getResult('ListAssocHydrator');
        $geoData = array_shift($rows);

        $q = $em->createNativeQuery('
            SELECT
                DISTINCT vup.fullname
            FROM
                inventory_participant ip
                INNER JOIN view_user_person vup ON vup.user_id = ip.participant_id
            WHERE
                ip.inventory_did = :id',
            new ResultSetMapping());
        $q->setParameter('id', $command->id);

        $participants = $q->getResult('ListAssocHydrator');

        $members = [];
        foreach ($participants as $participant) {
            $members[] = ['position' => '', 'name' => Utils::getShortName($participant['fullname']),];
        }

        $q = $em->createNativeQuery('
            SELECT
                fullname
            FROM
                view_user_person
            WHERE
                user_id = :user_id',
            new ResultSetMapping());
        $q->setParameter('user_id', $inventory->getResponsibleId());

        $rows = $q->getResult('ListAssocHydrator');
        $responsible = array_shift($rows);

        $q = $em->createNativeQuery('
            WITH all_count AS (
                SELECT
                    CASE WHEN ip.inventory_did IS NULL 
                        THEN ic.inventory_did 
                        ELSE ip.inventory_did 
                    END AS inventory_did,
                    CASE WHEN ip.base_product_id IS NULL 
                        THEN ic.base_product_id 
                        ELSE ip.base_product_id 
                    END AS base_product_id,
                    COALESCE(ip.initial_quantity, 0) AS initial_quantity,
                    COALESCE(ip.purchase_price, 0) AS purchase_price,
                    COALESCE(ip.retail_price, 0) AS retail_price,
                    SUM( COALESCE(ic.found_quantity, 0) ) AS found_quantity 
                FROM
                    inventory_product ip
                    FULL JOIN inventory_product_counter ic ON ic.inventory_did = ip.inventory_did 
                    AND ic.base_product_id = ip.base_product_id 
                WHERE
                    ip.inventory_did = :id
                GROUP BY
                    CASE WHEN ip.inventory_did IS NULL 
                        THEN ic.inventory_did 
                        ELSE ip.inventory_did 
                    END,
                    CASE WHEN ip.base_product_id IS NULL 
                        THEN ic.base_product_id 
                        ELSE ip.base_product_id 
                    END,
                    COALESCE(ip.initial_quantity, 0),
                    COALESCE(ip.purchase_price, 0),
                    COALESCE(ip.retail_price, 0) 
            ) 
            SELECT
                ac.inventory_did AS inventory_id,
                ac.base_product_id AS id,
                bp.name AS name,
                ac.initial_quantity,
                ac.purchase_price,
                ac.retail_price,
                ac.found_quantity,
                bpbc.bar_code
            FROM
                all_count ac
                LEFT JOIN base_product bp ON bp.id = ac.base_product_id
                LEFT JOIN base_product_bar_code bpbc ON bpbc.base_product_id = bp.id
        ', new ResultSetMapping());
        $q->setParameter('id', $command->id);

        $rows = $q->getResult('ListAssocHydrator');

        $data = [
            'number' => $inventory->getNumber(),
            'location' => !empty($geoData) ? 'г. '.$geoData['geo_city'].', '.$geoData['geo_point'].', '.$geoData['name'] : '',
            'org_name' => '',
            'org_unit' => '',
            'completed_at' => $inventory->getCompletedAt() ? $inventory->getCompletedAt()->format('d.m.Y') : '',
            'created_at' => $inventory->getCreatedAt()->format('d.m.Y'),
            'responsiblePeoples' => $members,
            'items' => [],
            'chairman' => ['position' => '', 'name' => !empty($responsible['fullname']) ? Utils::getShortName($responsible['fullname']) : '',],
            'members' => $members,
            'checker' => ['position' => '', 'name' => '',],
        ];

        foreach ($rows as $row) {
            $data['items'][] = [
                'name' => $row['name'],
                'code' => $row['id'],
                'barcode' => $row['bar_code'] ?: '',
                'initial_quantity' => $row['initial_quantity'],
                'initial_sum' => Number::price2Float($row['initial_quantity'] * $row['purchase_price']),
                'found_quantity' => $row['found_quantity'],
                'found_sum' => Number::price2Float($row['found_quantity'] * $row['purchase_price']),
            ];
        }

        /**
         * @var PdfService $service
         */
        $service = $this->get('service.pdf');
        $service->inventory($data, $command->fileName);
    }
}