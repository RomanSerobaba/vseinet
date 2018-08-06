<?php 

namespace SupplyBundle\Bus\Data\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\DocumentTypeCode;
use AppBundle\Enum\GoodsConditionCode;
use AppBundle\Enum\GoodsNeedRegisterType;
use AppBundle\Enum\OperationTypeCode;
use AppBundle\Enum\OrderTypeCode;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Specification\ViewSupplierProductSpecification;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use ServiceBundle\Components\Number;
use SupplyBundle\Component\ShipmentComponent;
use SupplyBundle\Entity\Supplier;
use SupplyBundle\Entity\SupplierInvoice;
use SupplyBundle\Entity\Supply;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UploadingQueryHandler extends MessageHandler
{
    const EXT_CSV = 'csv';
    const EXT_EXCEL = 'xls';
    const EXT_EXCEL_EXT = 'xlsx';

    const COLUMN_CODE = 'Код';
    const COLUMN_NAME = 'Товар';
    const COLUMN_QUANTITY = 'Количество';
    const COLUMN_PRICE = 'Цена';

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

    public function handle(UploadingQuery $command)
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
//        $currentUserId = $currentUser->getId();
        $currentUserId = 8;

        $supply = $em->getRepository(Supply::class)->find($command->id);
        if (!$supply instanceof Supply) {
            throw new NotFoundHttpException('Supply не найден');
        }

        if (!$command->filename instanceof UploadedFile) {
            throw new BadRequestHttpException('Файл не загружен');
        }

        $filePath = $command->filename->getPathname();

        $ext = strtolower(pathinfo($command->filename->getClientOriginalName(), PATHINFO_EXTENSION));

        if ($ext === self::EXT_CSV) {
            $supplyItems = $this->_processCsvFile($filePath);
        } elseif ($ext === self::EXT_EXCEL || $ext === self::EXT_EXCEL_EXT) {
            $supplyItems = $this->_processExcelFile($filePath);
        } else {
            throw new BadRequestHttpException('Unknown file extension '.$ext);
        }

//        print_r($supplyItems);
//        exit;

        unlink($filePath);

//        $supplyItems = [];
//        $supplyItems[] = ['code' => 1000604, 'name' => 'asdad', 'quantity' => 1, 'price' => 100000,];
//        $supplyItems[] = ['code' => 915477, 'name' => 'asdad', 'quantity' => 2, 'price' => 1200000,];
//        $supplyItems[] = ['code' => 1120391, 'name' => 'asdad', 'quantity' => 3, 'price' => 1300000,];

        if (!$supplyItems) {
            return $supplierProducts;
        }

        $spec = new ViewSupplierProductSpecification();

        $values = [];
        foreach ($supplyItems as $supplyItem) {
            $code = !empty($supplyItem['code']) ? "'".$supplyItem['code']."'" : 'NULL';
            $price = !empty($supplyItem['price']) ? $supplyItem['price'] : 'NULL';
            $values[] = sprintf("(%s, '%s', %u, %u)", $code, addslashes($supplyItem['name']), $supplyItem['quantity'], $price);
        }

        $sql = '
            WITH DATA ( code, name, quantity, price ) AS 
            ( VALUES '.implode(',', $values).' ) 
            SELECT
                sp.base_product_id,
                d.quantity,
                COALESCE ( d.price, sp.price ) AS price 
            FROM
                DATA AS d
                '.$spec->buildInnerJoin(null, $supply->getSupplierId(), 'd.code').'
            WHERE
                d.code IS NOT NULL '.$spec->buildWhere().'
            
            UNION ALL
            
            SELECT 
                COALESCE ( sp.base_product_id, bp.id ) AS base_product_id,
                d.quantity,
                COALESCE ( d.price, sp.price ) AS price 
            FROM
                DATA AS d
                '.$spec->buildLeftJoin(null, $supply->getSupplierId(), null, 'd.name').'
                LEFT JOIN base_product AS bp ON d.name = bp.name 
            WHERE
                d.code IS NULL '.$spec->buildWhere().'       
        ';

//        echo $sql;
//        exit;

        $query = $em->createNativeQuery($sql, new ResultSetMapping());
        $query->setParameter('supplier_id', $supply->getSupplierId());

        $reserves = $supplyItems = [];
        $rows = $query->getResult('ListAssocHydrator');

        foreach ($rows as $row) {
            $supplyItems[$row['base_product_id']][$row['price']] = $row['quantity'];
        }

//        print_r($supplyItems);
//        exit;

        $em->getConnection()->beginTransaction();
        try {
            // Очищаем регистр по счету
            $statement = $em->getConnection()->prepare('
                INSERT INTO goods_need_register ( base_product_id, delta, order_item_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                SELECT
                    srr.base_product_id,
                    SUM( srr.delta ),
                    srr.order_item_id,
                    COALESCE ( sr.id, ssr.id, sr2.id ),
                    :supplier_reserve :: document_type_code,
                    COALESCE ( sr.created_at, ssr.created_at, sr2.created_at ),
                    :supply_item_adding :: operation_type_code,
                    now( ),
                    :user_id :: INTEGER
                FROM
                    supplier_reserve_register AS srr
                    LEFT JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id
                    JOIN supplier_reserve AS sr2 ON sr2.supplier_id = srr.supplier_id 
                    AND sr2.is_shipping = FALSE 
                    AND sr2.closed_at
                    IS NULL LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = srr.supplier_id 
                    AND ssr.is_shipping = TRUE 
                    AND ssr.closed_at IS NULL 
                WHERE
                    srr.supply_id = :supply_id 
                    AND srr.supplier_reserve_id IS NULL 
                GROUP BY
                    srr.base_product_id,
                    srr.order_item_id,
                    sr.id,
                    ssr.id,
                    sr2.id 
                HAVING
                    SUM( srr.delta ) > 0
            ');
            $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
            $statement->bindValue('supply_item_adding', OperationTypeCode::SUPPLY_ITEM_ADDING);
            $statement->bindValue('user_id', $currentUserId);
            $statement->bindValue('supply_id', $command->id);
            $statement->execute();

            $statement = $em->getConnection()->prepare('
                INSERT INTO supplier_reserve_register ( base_product_id, delta, order_item_id, supplier_reserve_id, supply_id, purchase_price, supplier_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                SELECT
                    srr.base_product_id,
                    SUM( - srr.delta ),
                    srr.order_item_id,
                    sr.id,
                    srr.supply_id,
                    srr.purchase_price,
                    srr.supplier_id,
                    COALESCE ( sr.id, ssr.id, sr2.id ),
                    :supplier_reserve :: document_type_code,
                    COALESCE ( sr.created_at, ssr.created_at, sr2.created_at ),
                    :supply_item_adding :: operation_type_code,
                    now( ),
                    :user_id :: INTEGER
                FROM
                    supplier_reserve_register AS srr
                    LEFT JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id
                    JOIN supplier_reserve AS sr2 ON sr2.supplier_id = srr.supplier_id 
                        AND sr2.is_shipping = FALSE 
                        AND sr2.closed_at IS NULL 
                    LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = srr.supplier_id 
                        AND ssr.is_shipping = TRUE 
                        AND ssr.closed_at IS NULL 
                WHERE
                    srr.supply_id = :supply_id 
                GROUP BY
                    srr.base_product_id,
                    srr.order_item_id,
                    srr.supplier_reserve_id,
                    srr.purchase_price,
                    sr.id,
                    ssr.id,
                    srr.supplier_id,
                    sr2.id,
                    srr.supply_id 
                HAVING
                    SUM( srr.delta ) > 0 
                
                UNION ALL
                
                SELECT
                    srr.base_product_id,
                    SUM( srr.delta ),
                    srr.order_item_id,
                    sr.id,
                    NULL,
                    srr.purchase_price,
                    srr.supplier_id,
                    COALESCE ( sr.id, ssr.id, sr2.id ),
                    :supplier_reserve :: document_type_code,
                    COALESCE ( sr.created_at, ssr.created_at, sr2.created_at ),
                    :supply_item_adding :: operation_type_code,
                    now( ),
                    :user_id :: INTEGER 
                FROM
                    supplier_reserve_register AS srr
                    LEFT JOIN supplier_reserve AS sr ON sr.id = srr.supplier_reserve_id
                    JOIN supplier_reserve AS sr2 ON sr2.supplier_id = srr.supplier_id 
                        AND sr2.is_shipping = FALSE 
                        AND sr2.closed_at IS NULL 
                    LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = srr.supplier_id 
                        AND ssr.is_shipping = TRUE 
                        AND ssr.closed_at IS NULL 
                WHERE
                    srr.supply_id = :supply_id 
                    AND srr.supplier_reserve_id > 0 
                GROUP BY
                    srr.base_product_id,
                    srr.order_item_id,
                    srr.supplier_reserve_id,
                    srr.purchase_price,
                    sr.id,
                    ssr.id,
                    srr.supplier_id,
                    sr2.id 
                HAVING
                    SUM( srr.delta ) > 0
            ');
            $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
            $statement->bindValue('supply_item_adding', OperationTypeCode::SUPPLY_ITEM_ADDING);
            $statement->bindValue('user_id', $currentUserId, Type::INTEGER);
            $statement->bindValue('supply_id', $command->id);
            $statement->execute();

            $sql = '
                SELECT
                    gnr.id,
                    gnr.base_product_id,
                    gnr.need_quantity,
                    gnr.supplier_reserve_id,
                    gnr.purchase_price 
                FROM (
                    (
                    SELECT
                        SUM( srr.delta ) AS need_quantity,
                        srr.base_product_id,
                        srr.order_item_id AS id,
                        srr.purchase_price,
                        srr.supplier_reserve_id 
                    FROM
                        supply AS si
                        JOIN supplier_reserve AS sr ON sr.supplier_id = si.supplier_id 
                            AND sr.is_shipping = FALSE
                            AND sr.closed_at IS NULL 
                        LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = sr.supplier_id 
                            AND ssr.is_shipping = TRUE 
                            AND ssr.closed_at IS NULL 
                        JOIN supplier_reserve_register AS srr ON srr.supplier_reserve_id = COALESCE ( ssr.id, sr.id ) 
                    WHERE
                        si.id = :supply_id 
                    GROUP BY
                        srr.base_product_id,
                        srr.order_item_id,
                        srr.purchase_price,
                        srr.supplier_reserve_id 
                    HAVING
                        SUM( srr.delta ) > 0 
                    ) 
                        
                    UNION ALL
                        
                    (
                    SELECT
                        gnr.delta AS need_quantity,
                        gnr.base_product_id,
                        gnr.order_item_id AS id,
                        bp.supplier_price,
                        NULL AS supplier_reserve_id 
                    FROM
                        get_goods_need_register_data (CURRENT_TIMESTAMP::TIMESTAMP) AS gnr
                        JOIN order_item AS oi ON oi.id = gnr.order_item_id
                        JOIN base_product AS bp ON bp.id = gnr.base_product_id
                        JOIN supply AS s ON s.supplier_id = bp.supplier_id 
                    WHERE
                        s.id = :supply_id 
                    ) 
                ) AS gnr
                    JOIN order_item AS oi ON oi.id = gnr.id
                    JOIN "order" AS o ON o.id = oi.order_id 
                ORDER BY
                    gnr.supplier_reserve_id,
                    CASE WHEN o.type_code IN ( :site, :shop, :legal, :request ) THEN 0 ELSE 1 END,
                    o.created_at
            ';

            $query = $em->createNativeQuery($sql, new ResultSetMapping());
            $query->setParameter('supply_id', $command->id);
            $query->setParameter('site', OrderTypeCode::SITE);
            $query->setParameter('shop', OrderTypeCode::SHOP);
            $query->setParameter('legal', OrderTypeCode::LEGAL);
            $query->setParameter('request', OrderTypeCode::REQUEST);

            $orderItems = $query->getResult('ListAssocHydrator');

            foreach ($orderItems as $orderItem) {
                if (isset($supplyItems[$orderItem['base_product_id']])) {
                    foreach ($supplyItems[$orderItem['base_product_id']] as $invoiceItemPrice => $invoiceItemQuantity) {
                        if ($orderItem['need_quantity'] < $invoiceItemQuantity) {
                            $reserves[] = [
                                'order_item_id' => $orderItem['id'],
                                'quantity' => $orderItem['need_quantity'],
                                'supplier_reserve_id' => $orderItem['supplier_reserve_id'],
                                'old_purchase_price' => $orderItem['purchase_price'],
                                'purchase_price' => $invoiceItemQuantity,
                                'base_product_id' => $orderItem['base_product_id'],
                            ];

                            $supplyItems[$orderItem['base_product_id']][$invoiceItemPrice] -= $orderItem['need_quantity'];
                            break;
                        } else {
                            $reserves[] = [
                                'order_item_id' => $orderItem['id'],
                                'quantity' => $invoiceItemQuantity,
                                'supplier_reserve_id' => $orderItem['supplier_reserve_id'],
                                'old_purchase_price' => $orderItem['purchase_price'],
                                'purchase_price' => $invoiceItemPrice,
                                'base_product_id' => $orderItem['base_product_id'],
                            ];

                            $orderItem['need_quantity'] -= $invoiceItemQuantity;
                            unset($supplyItems[$orderItem['base_product_id']][$invoiceItemPrice]);
                        }
                    }
                }
            }

            $values = [];

//            print_r($reserves);
//            exit;

            foreach ($reserves as $reserve) {
                $values[] = sprintf(
                    '(%u::INTEGER,%u::INTEGER,%u::INTEGER,%u::INTEGER,%u::INTEGER,%u::INTEGER)',
                    $reserve['base_product_id'],
                    $reserve['quantity'],
                    $reserve['order_item_id'],
                    $reserve['purchase_price'],
                    $reserve['old_purchase_price'],
                    $reserve['supplier_reserve_id']
                );
            }

            if ($values) {
                // Проводим новый резерв
                $statement = $em->getConnection()->prepare('
                    WITH DATA ( base_product_id, quantity, order_item_id, supplier_reserve_id ) AS 
                    ( VALUES '.implode(',', $values).' )  
                    INSERT INTO goods_need_register ( base_product_id, delta, order_item_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                    SELECT
                        d.base_product_id,
                        - SUM( d.quantity ),
                        d.order_item_id,
                        COALESCE ( ssr.id, sr.id ),
                        :supplier_reserve :: document_type_code,
                        COALESCE ( ssr.created_at, sr.created_at ),
                        :supply_item_adding :: operation_type_code,
                        now( ),
                        :user_id :: INTEGER 
                    FROM
                        DATA AS d
                        JOIN supply AS s ON s.id = :supply_id
                        JOIN supplier_reserve AS sr ON sr.supplier_id = s.supplier_id 
                            AND sr.is_shipping = FALSE 
                            AND sr.closed_at IS NULL 
                        LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = s.supplier_id 
                            AND ssr.is_shipping = TRUE 
                            AND ssr.closed_at IS NULL 
                    WHERE
                        d.supplier_reserve_id IS NULL 
                    GROUP BY
                        d.base_product_id,
                        d.order_item_id,
                        ssr.id,
                        sr.id        
                ');
                $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
                $statement->bindValue('supply_item_adding', OperationTypeCode::SUPPLY_ITEM_ADDING);
                $statement->bindValue('user_id', $currentUserId, Type::INTEGER);
                $statement->bindValue('supply_id', $command->id);
                $statement->execute();

                $statement = $em->getConnection()->prepare('
                    WITH DATA ( base_product_id, quantity, order_item_id, purchase_price, old_purchase_price, supplier_reserve_id ) AS
                    ( VALUES '.implode(',', $values).' )   
                    INSERT INTO supplier_reserve_register ( base_product_id, delta, order_item_id, supplier_reserve_id, supply_id, purchase_price, supplier_id, registrator_id, registrator_type_code, registered_at, register_operation_type_code, created_at, created_by ) 
                    SELECT
                        d.base_product_id,
                        - d.quantity,
                        d.order_item_id,
                        d.supplier_reserve_id,
                        NULL,
                        d.old_purchase_price,
                        s.supplier_id,
                        sr.id,
                        :supplier_reserve :: document_type_code,
                        sr.created_at,
                        :supply_item_adding :: operation_type_code,
                        now( ),
                        :user_id :: INTEGER
                    FROM
                        DATA AS d
                        JOIN supplier_reserve AS sr ON sr.id = d.supplier_reserve_id
                        JOIN supply AS s ON s.id = :supply_id 
                    WHERE
                        d.supplier_reserve_id > 0
                    
                    UNION ALL
                    
                    SELECT
                        d.base_product_id,
                        d.quantity,
                        d.order_item_id,
                        d.supplier_reserve_id,
                        s.id,
                        d.purchase_price,
                        s.supplier_id,
                        COALESCE ( sr.id, ssr.id, sr2.id ),
                        :supplier_reserve :: document_type_code,
                        COALESCE ( sr.created_at, ssr.created_at, sr2.created_at ),
                        :supply_item_adding :: operation_type_code,
                        now( ),
                        :user_id :: INTEGER 
                    FROM
                        DATA AS d
                        JOIN supply AS s ON s.id = :supply_id     
                        LEFT JOIN supplier_reserve AS sr ON sr.id = d.supplier_reserve_id
                        JOIN supplier_reserve AS sr2 ON sr2.supplier_id = d.supplier_reserve_id 
                            AND sr2.is_shipping = FALSE 
                            AND sr2.closed_at IS NULL 
                        LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = s.supplier_id 
                            AND ssr.is_shipping = TRUE 
                            AND ssr.closed_at IS NULL 
                ');
                $statement->bindValue('supplier_reserve', DocumentTypeCode::SUPPLIER_RESERVE);
                $statement->bindValue('supply_item_adding', OperationTypeCode::SUPPLY_ITEM_ADDING);
                $statement->bindValue('user_id', $currentUserId, Type::INTEGER);
                $statement->bindValue('supply_id', $command->id);
                $statement->execute();
            }

            $em->getConnection()->commit();
        } catch (\Exception $ex) {
            $em->getConnection()->rollback();

            throw $ex;
        }

        return $this->camelizeKeys($reserves, ['old_purchase_price', 'purchase_price',]);
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
            self::COLUMN_CODE => 'code',
            self::COLUMN_NAME => 'name',
            self::COLUMN_QUANTITY => 'quantity',
            self::COLUMN_PRICE => 'price',
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
                $code = !empty($list[0]) ? (int) $list[0] : 0;
                $name = !empty($list[1]) ? (int) $list[1] : '';
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
            self::COLUMN_CODE => 'code',
            self::COLUMN_NAME => 'name',
            self::COLUMN_QUANTITY => 'quantity',
            self::COLUMN_PRICE => 'price',
        ];

        $columnsIndex = [
            self::COLUMN_CODE => false,
            self::COLUMN_NAME => false,
            self::COLUMN_QUANTITY => false,
            self::COLUMN_PRICE => false,
        ];

        for ($i = 1; $i <= 4; $i++) {
            $cellCode = $sheet->getCellByColumnAndRow($i, 1);
            $value = (string) $cellCode->getValue();
            if (isset($columnsIndex[$value])) {
                $columnsIndex[$value] = $i;
            }
        }

        if (empty($columnsIndex[self::COLUMN_NAME]) || empty($columnsIndex[self::COLUMN_QUANTITY])) {
            throw new BadRequestHttpException('Загружаемый файл не соответствует шаблону, не найдены колонки "'.self::COLUMN_NAME.'" и "'.self::COLUMN_QUANTITY.'"');
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

                    if ($name === self::COLUMN_NAME && empty($value)) {
//                        throw new BadRequestHttpException('Значение в поле "'.self::COLUMN_NAME.'" пусто. Строка '.$rowNumber);
                        $row = [];
                        break;
                    }

                    $row[$columns[$name]] = $value;
                }
            }

            if ($row) {
                $rows[] = $row;
            }
        }

        return $rows;
    }
}