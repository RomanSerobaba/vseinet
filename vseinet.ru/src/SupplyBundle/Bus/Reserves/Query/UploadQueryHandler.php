<?php 

namespace SupplyBundle\Bus\Reserves\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\OperationTypeCode;
use AppBundle\Enum\OrderTypeCode;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Specification\ViewSupplierProductSpecification;
use Doctrine\ORM\Query\ResultSetMapping;
use SupplyBundle\Entity\Supplier;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UploadQueryHandler extends MessageHandler
{
    const EXT_CSV = 'csv';
    const EXT_EXCEL = 'xls';

    /**
     * Максимальный номер колоки поиска заголовков
     */
    const HEADERS_MAX_COLUMN = 65;

    /**
     * Максимальный номер стоки поиска заголовков
     */
    const HEADERS_MAX_ROW = 100;

    /**
     * Количество считываемых строк за раз
     */
    const READ_CHUNK_SIZE = 1000;

    public function handle(UploadQuery $command)
    {
        $supplierProducts = ['products' => [], 'orders' => [],];

        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $supplier = $em->getRepository(Supplier::class)->find($command->id);
        if (!$supplier instanceof Supplier) {
            throw new NotFoundHttpException('Поставщик не найден');
        }

        if (!$command->filename instanceof UploadedFile) {
            throw new BadRequestHttpException('Файл не загружен');
        }

        $filePath = $command->filename->getPathname();

        $ext = strtolower(pathinfo($command->filename->getClientOriginalName(), PATHINFO_EXTENSION));

        if ($ext === self::EXT_CSV) {
            $invoiceItems = $this->_processCsvFile($filePath);
        } elseif ($ext === self::EXT_EXCEL) {
            $invoiceItems = $this->_processExcelFile($filePath);
        } else {
            throw new BadRequestHttpException('Unknown file extension '.$ext);
        }

        unlink($filePath);

        if (!$invoiceItems) {
            return $supplierProducts;
        }

        $values = [];
        foreach ($invoiceItems as $invoiceItem) {
            $code = !empty($invoiceItem['code']) ? "'".$invoiceItem['code']."'" : 'NULL';
            $price = !empty($invoiceItem['price']) ? $invoiceItem['price']: 'NULL';
            $values[] = sprintf("(%s, '%s', %u, %s)", $code, $invoiceItem['name'] ?? '', $invoiceItem['quantity'] ?? 0, $price);
        }

        $spec = new ViewSupplierProductSpecification();

        $sql = '
            WITH DATA ( code, name, quantity, price ) AS 
            ( VALUES '.implode(',', $values).' ) 
            SELECT
                bp.id AS base_product_id,
                d.quantity,
                COALESCE ( d.price, sp.price ) 
            FROM
                base_product AS bp
                '.$spec->buildLeftJoin('bp.id').'
                JOIN DATA AS d ON d.code IS NOT NULL 
                    AND d.code = sp.code 
            WHERE '.$spec->buildWhere(false).'
            
            UNION ALL
            
            SELECT
                bp.id AS base_product_id,
                d.quantity,
                COALESCE ( d.price, sp.price ) AS price 
            FROM
                base_product AS bp
                '.$spec->buildLeftJoin('bp.id').'
                JOIN DATA AS d ON d.code IS NULL AND d.name = COALESCE ( sp.name, bp.name ) 
            WHERE '.$spec->buildWhere(false).'                       
        ';

        $query = $em->createNativeQuery($sql, new ResultSetMapping());

        $invoiceItems = $reserves = [];
        $rows = $query->getResult('ListAssocHydrator');

        foreach ($rows as $row) {
            $invoiceItems[$row['base_product_id']][$row['price']] = $row['quantity'];
        }

        $items = [];
        $geoPointSQL = $command->pointId > 0 ? 'AND o.geo_point_id = '.$command->pointId : '';

        $sql = "
            SELECT
                gnr.id,
                gnr.base_product_id,
                gnr.need_quantity 
            FROM
                (
                ".($command->withConfirmedReserves ? " 
                SELECT
                SUM( srr.delta ) AS need_quantity,
                srr.base_product_id,
                srr.order_item_id AS id,
                TRUE AS is_reserved 
            FROM
                supplier_reserve_register AS srr
                JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id 
                AND sr.is_shipping = FALSE 
                AND sr.closed_at
                IS NULL JOIN order_item AS oi ON oi.id = srr.order_item_id
                JOIN \"order\" AS o ON o.id = oi.order_id 
            WHERE
                sr.supplier_id = :supplier_id ".$geoPointSQL."
            GROUP BY
                srr.base_product_id,
                srr.order_item_id 
            HAVING
                SUM( srr.delta ) > 0 
            UNION ALL
                " : ''). " 
            SELECT
                CASE WHEN srr.quantity > gnr.delta 
                    THEN gnr.delta 
                    ELSE srr.quantity 
                END AS need_quantity,
                gnr.base_product_id,
                gnr.order_item_id AS id,
                FALSE AS is_reserved 
            FROM
                get_goods_need_register_data(CURRENT_TIMESTAMP::TIMESTAMP) AS gnr
                JOIN order_item AS oi ON oi.id = gnr.order_item_id
                JOIN base_product AS bp ON bp.id = gnr.base_product_id
                JOIN supplier_reserve_request AS srr ON srr.supplier_id = bp.supplier_id 
                AND srr.order_item_id = oi.id
                JOIN \"order\" AS o ON o.id = oi.order_id 
            WHERE
                bp.supplier_id = :supplier_id ".$geoPointSQL." 
                ) AS gnr
                JOIN order_item AS oi ON oi.id = gnr.id
                JOIN \"order\" AS o ON o.id = oi.order_id 
            ORDER BY
                gnr.is_reserved DESC,
                CASE WHEN o.type_code IN ( :site, :shop, :legal, :request ) 
                    THEN 0 
                    ELSE 1 
                END,
                o.created_at
        ";

        $query = $em->createNativeQuery($sql, new ResultSetMapping());
        $query->setParameter('supplier_id', $command->id);
        $query->setParameter('site', OrderTypeCode::SITE);
        $query->setParameter('shop', OrderTypeCode::SHOP);
        $query->setParameter('legal', OrderTypeCode::LEGAL);
        $query->setParameter('request', OrderTypeCode::REQUEST);

        $orderItems = $query->getResult('ListAssocHydrator');

        $reserves = [];

        foreach ($orderItems as $orderItem) {
            if (isset($invoiceItems[$orderItem['base_product_id']])) {
                foreach ($invoiceItems[$orderItem['base_product_id']] as $invoiceItemPrice => $invoiceItemQuantity) {
                    if ($orderItem['need_quantity'] < $invoiceItemQuantity) {
                        $reserves[] = ['order_item_id' => $orderItem['id'], 'quantity' => $orderItem['need_quantity'], 'purchase_price' => $invoiceItemPrice, 'base_product_id' => $orderItem['base_product_id'],];
                        $invoiceItems[$orderItem['base_product_id']][$invoiceItemPrice] -= $orderItem['need_quantity'];

                        break;
                    } else {
                        $reserves[] = ['order_item_id' => $orderItem['id'], 'quantity' => $invoiceItemQuantity, 'purchase_price' => $invoiceItemPrice, 'base_product_id' => $orderItem['base_product_id'],];
                        $orderItem['need_quantity'] -= $invoiceItemQuantity;
                        unset($invoiceItems[$orderItem['base_product_id']][$invoiceItemPrice]);
                    }

                    if ($orderItem['need_quantity'] > 0) {
                        $statement = $em->getConnection()->prepare('
                            UPDATE supplier_product 
                            SET product_availability_code = :out_of_stock 
                            WHERE
                                supplier_id = :supplier_id 
                                AND base_product_id = :base_product_id
                        ');
                        $statement->bindValue('out_of_stock', ProductAvailabilityCode::OUT_OF_STOCK);
                        $statement->bindValue('base_product_id', $orderItem['base_product_id']);
                        $statement->bindValue('supplier_id', $command->id);
                        $statement->execute();
                    }
                }
            }
        }

        $em->getConnection()->beginTransaction();
        try {
            // Обнуляем резервы
            $statement = $em->getConnection()->prepare('
                INSERT INTO goods_need_register ( base_product_id, delta, order_item_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                SELECT
                    oi.base_product_id,
                    SUM( srr.delta ),
                    oi.id AS order_item_id,
                    sr.id,
                    :supplier_reserve,
                    sr.created_at,
                    :supplier_reserve_change,
                    now( ),
                    :user_id::INTEGER
                FROM
                    supplier_reserve_register AS srr
                    JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id
                    JOIN order_item AS oi ON oi.id = srr.order_item_id
                    JOIN "order" AS o ON o.id = oi.order_id 
                WHERE
                    sr.supplier_id = :supplier_id 
                    AND sr.is_shipping = FALSE 
                    AND sr.closed_at IS NULL '.$geoPointSQL.'
                GROUP BY
                    oi.id,
                    sr.id
            ');
            $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
            $statement->bindValue('supplier_reserve_change', OperationTypeCode::SUPPLIER_RESERVE_CHANGE);
            $statement->bindValue('user_id', $currentUser->getId());
            $statement->bindValue('supplier_id', $command->id);
            $statement->execute();

            $statement = $em->getConnection()->prepare('
                INSERT INTO supplier_reserve_register ( base_product_id, delta, order_item_id, supply_id, supplier_reserve_id, purchase_price, supplier_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                SELECT
                    oi.base_product_id,
                    SUM( - srr.delta ),
                    oi.id AS order_item_id,
                    srr.supply_id,
                    sr.id,
                    srr.purchase_price,
                    srr.supplier_id,
                    sr.id,
                    :supplier_reserve,
                    sr.created_at,
                    :supplier_reserve_change,
                    NOW( ),
                    :user_id::INTEGER 
                FROM
                    supplier_reserve_register AS srr
                    JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id
                    JOIN order_item AS oi ON oi.id = srr.order_item_id
                    JOIN "order" AS o ON o.id = oi.order_id 
                WHERE
                    sr.supplier_id = :supplier_id 
                    AND sr.is_shipping = FALSE 
                    AND sr.closed_at IS NULL '.$geoPointSQL.' 
                GROUP BY
                    oi.id,
                    sr.id,
                    srr.supply_id,
                    srr.purchase_price,
                    srr.supplier_id
            ');
            $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
            $statement->bindValue('supplier_reserve_change', OperationTypeCode::SUPPLIER_RESERVE_CHANGE);
            $statement->bindValue('user_id', $currentUser->getId());
            $statement->bindValue('supplier_id', $command->id);
            $statement->execute();

            // Проводим новый резерв
            $values = [];

            foreach ($reserves as $reserve) {
                $values[] = sprintf('(%u::INTEGER,%u::INTEGER,%u::INTEGER)', $reserve['base_product_id'], $reserve['quantity'], $reserve['order_item_id']);
            }

            if ($values) {
                $statement = $em->getConnection()->prepare('
                    WITH DATA ( base_product_id, quantity, order_item_id ) AS 
                    ( VALUES  '.implode(',', $values).' ) 
                    INSERT INTO goods_need_register ( base_product_id, delta, order_item_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                    SELECT
                        d.base_product_id,
                        - SUM( d.quantity ),
                        d.order_item_id,
                        sr.id,
                        :supplier_reserve,
                        sr.created_at,
                        :supplier_reserve_change,
                        now( ),
                        :user_id::INTEGER 
                    FROM
                        DATA AS d
                        JOIN supplier_reserve AS sr ON sr.supplier_id = :supplier_id 
                        AND sr.is_shipping = FALSE 
                        AND sr.closed_at IS NULL 
                    GROUP BY
                        d.base_product_id,
                        d.order_item_id,
                        sr.id
                ');
                $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
                $statement->bindValue('supplier_reserve_change', OperationTypeCode::SUPPLIER_RESERVE_CHANGE);
                $statement->bindValue('user_id', $currentUser->getId());
                $statement->bindValue('supplier_id', $command->id);
                $statement->execute();
            }

            $values = [];

            foreach ($reserves as $reserve) {
                $values[] = sprintf('(%u::INTEGER,%u::INTEGER,%u::INTEGER,%u::INTEGER)', $reserve['base_product_id'], $reserve['quantity'], $reserve['order_item_id'], $reserve['purchase_price']);
            }

            if ($values) {
                $statement = $em->getConnection()->prepare('
                    WITH DATA ( base_product_id, quantity, order_item_id, purchase_price ) AS 
                    ( VALUES '.implode(',', $values).' )
                    INSERT INTO goods_need_register ( base_product_id, delta, order_item_id, purchase_price, supplier_reserve_id, supplier_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                    SELECT
                        d.base_product_id,
                        d.quantity,
                        d.order_item_id,
                        d.purchase_price,
                        sr.id AS supplier_reserve_id,
                        sr.supplier_id,
                        sr.id,
                        \'supplier_reserve\',
                        sr.created_at,
                        \'supplier_reserve_change\',
                        now( ),
                        : user_id 
                    FROM
                        DATA AS d
                        JOIN supplier_reserve AS sr ON sr.supplier_id = : supplierId 
                        AND sr.is_shipping = FALSE 
                        AND sr.closed_at IS NULL
                ');
                $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
                $statement->bindValue('supplier_reserve_change', OperationTypeCode::SUPPLIER_RESERVE_CHANGE);
                $statement->bindValue('user_id', $currentUser->getId());
                $statement->bindValue('supplier_id', $command->id);
                $statement->execute();
            }

            $em->getConnection()->commit();
        } catch (\Exception $ex) {
            $em->getConnection()->rollback();

            throw $ex;
        }

        return $this->camelizeKeys($reserves, ['purchase_price',]);
    }

    /**
     * @param string $file
     *
     * @return array
     */
    private function _processCsvFile(string $file) : array
    {
        $f = fopen($file, "r");
        $positions = [];

        $fields = [
            'Код' => 'code',
            'Номенклатура' => 'name',
            'Количество' => 'quantity',
            'Цена' => 'price',
        ];

        $index = 0;
        while (!feof($f)) {
            $row = trim(iconv("cp1251", "utf-8", fgets($f)));

            $list = explode(';', $row);

            if ($index == 0) {
                $columns = [];
                foreach ($list as $item) {
                    if (isset($fields[$item])) {
                        $columns[$fields[$item]] = true;
                    }
                }

                if ((empty($columns['code']) || empty($columns['name'])) || empty($columns['quantity'])) {
                    throw new BadRequestHttpException('Неверный формат файла');
                }
            } else {
                $code = !empty($list[0]) ? $list[0] : 0;
                $name = !empty($list[1]) ? $list[1] : '';
                $quantity = !empty($list[2]) ? (int) $list[2] : 0;
                $price = !empty($list[3]) ? (int) $list[3] : 0;

                $positions[] = ['code' => $code, 'name' => $name, 'quantity' => $quantity, 'price' => $price,];
            }

            $index++;
        }

        return $positions;
    }

    /**
     * @param string $file
     *
     * @return array
     */
    private function _processExcelFile(string $file) : array
    {
        return $this->loadPricelist($file);
    }

    /**
     * @param $filename
     *
     * @return array
     */
    protected function loadPricelist($filename) : array
    {
        $spreadsheet = IOFactory::load($filename);
        $sheet = $spreadsheet->getSheet(0);

        $columns = [
            'Код' => 'code',
            'Наименование' => 'name',
            'Количество' => 'quantity',
            'Цена' => 'price',
        ];

        $columnsIndex = [
            'Код' => false,
            'Наименование' => false,
            'Количество' => false,
            'Цена' => false,
        ];

        for ($i = 1; $i <= 4; $i++) {
            $cellCode = $sheet->getCellByColumnAndRow($i, 1);
            $value = (string) $cellCode->getValue();
            if (isset($columnsIndex[$value])) {
                $columnsIndex[$value] = $i;
            }
        }

        $rows = [];
        for ($rowNumber = 2; $rowNumber <= $sheet->getHighestDataRow(); $rowNumber++) {
            $row = [];
            foreach ($columnsIndex as $name => $index) {
                if ($index !== false) {
                    $cellCode = $sheet->getCellByColumnAndRow($index, $rowNumber);
                    $value = $cellCode->getValue();

                    if (is_object($value)) {
                        $value = $value->getPlainText();
                    }

                    $row[$columns[$name]] = $value;
                }
            }

            $rows[] = $row;
        }

        return $rows;
    }


    /**
     * @param      $value
     * @param bool $toLowerCase
     * @param bool $leaveWhitespace
     *
     * @return mixed|string
     */
    protected function clearValue($value, $toLowerCase = false, $leaveWhitespace = false)
    {
        $value = preg_replace(["/[\r|\n|\t|\v|\f|\s]/uD", '/\"+/uD'], [' ', '"'], $value);

        if ($toLowerCase) {
            $value = mb_strtolower($value, 'UTF-8');
        }

        if (!$leaveWhitespace) {
            $value = trim(preg_replace('/\s+/uD', ' ', $value));
        }

        return $value;
    }

    /**
     * @param $field
     * @param $value
     *
     * @return mixed|string
     */
    protected function clearValueSpecial($field, $value)
    {
        if ('description' != $field) {
            $value = $this->clearValue($value, false, false);
            if ('url' != $field) {
                $value = str_replace('_', ' ', $value);
            }
        }

        return $value;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param int                         $baseProductId
     * @param int                         $supplierId
     */
    protected function setOutOfStock(\Doctrine\ORM\EntityManager $em, int $baseProductId, int $supplierId) : void
    {
        $statement = $em->getConnection()->prepare('
            UPDATE supplier_product 
            SET product_availability_code = :out_of_stock 
            WHERE
                base_product_id = :base_product_id 
                AND supplier_id = :supplier_id
        ');
        $statement->bindValue('out_of_stock', ProductAvailabilityCode::OUT_OF_STOCK);
        $statement->bindValue('base_product_id', $baseProductId);
        $statement->bindValue('supplier_id', $supplierId);
        $statement->execute();
    }
}