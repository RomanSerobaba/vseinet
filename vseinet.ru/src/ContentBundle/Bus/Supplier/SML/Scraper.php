<?php 

namespace ContentBundle\Bus\Supplier\SML;

use AppBundle\Container\ContainerAware;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use SupplyBundle\Entity\Supplier;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Entity\Counter;
use ContentBundle\Entity\SMLMarkdownReason;
use Buzz\Exception\RequestException;

class Scraper extends ContainerAware
{
    const PER_PAGE = 100;

    const EXCLUDE_CATEGORIES = [
        298,    // Сувенирное оружие
        18950,  // Бытовая техника
        19838,  // Товары для охоты
    ];

    /**
     * @var array
     */
    protected $products;

    /**
     * @var array
     */
    protected $categories;

    /**
     * @var array
     */
    protected $newCategories;

    /**
     * @var array<SMLMarkdownReason>
     */
    protected $markdownReasons;


    public function grab()
    {
        // init
        $this->products = [];
        $this->categories = [];
        $this->newCategories = [];

        $em = $this->getDoctrine()->getManager();

        $supplier = $em->getRepository(Supplier::class)->findOneBy(['code' => 'SML']);
        if (!$supplier instanceof Supplier) {
            $error = 'Неизвестная ошибка: поставщик не найден';
            $logger->error($error);
            throw new BadRequestHttpException($error);
        }
        $counter = $em->getRepository(Counter::class)->findOneBy(['name' => 'SML-id-greater_than']); 
        
        $loader = $this->get('supplier.pricelist.loader')->init($supplier, true);

        $logger = $this->get('simple.logger')->setName('pricelist', 'sml');
        $logger->info(sprintf('Начало загрузки с id-greater-than = %d', $idGreaterThan = $counter->getValue()));

        $s = microtime(true);
        $externalIds = [];
        while ($loader->getMaxNumberProductsForLoad() - self::PER_PAGE > count($this->products)) {
            $response = $this->query('https://www.sima-land.ru/api/v3/item/', [
                'is_disabled' => 0,
                'has_balance' => 1,
                'has_price' => 1,
                'per-page' => self::PER_PAGE,
                'id-greater-than' => $counter->getValue(),
                'expand' => 'categories,description',
            ]);
            if (empty($response['items'])) {
                $counter->setValue(0);
                $logger->info('Круг завершен');
                break;
            }
            foreach ($response['items'] as $item) {
                $this->products[] = $item;
                $externalIds[] = $item['categories'][0];
                $counter->setValue($item['id']);
            }  
            echo '.';  
            break;   
        }
        $em->persist($counter);
        $em->flush($counter);
        $logger->info(sprintf('Получено %d товаров за %0.3f сек', count($this->products), microtime(true) - $s));

        if (empty($this->products)) {
            return;
        }

        $s = microtime(true);
        $this->updateCategories($supplier, $externalIds);
        $logger->info('Обновлено дерево категорий за %0.3f сек', microtime(true) - $s);

        foreach ($this->products as $product) {
            if ($product['is_markdown']) {
                if (!$this->markdownReasonIsDisabled($product['markdown_reason'])) {
                    continue;
                }
                $product['name'] = trim(str_ireplace('УЦЕНКА', '', $product['name']));
            }
           
            $category = $this->categories[$product['categories'][0]];
            if (preg_match('~(^|\.)('.implode('|', self::EXCLUDE_CATEGORIES).')($|\.)~isu', $category['path'])) {
                continue;
            }

            if (false !== ($pos = strpos($product['itemUrl'], '?'))) {
                $product['itemUrl'] = substr($product['itemUrl'], 0, $pos - 1);
            }

            $data = [
                'code' => $product['sid'],
                'name' => $product['name'].' ('.substr($product['id'], -3).')',
                'price' => $product['price'],
                'min_quantity' => round($product['minimum_order_quantity']),
                'description' => empty($product['description']) ? '' : $this->clearDescription($product['description']),
                'category_id' => $category['id'],
                'url' => 'https://www.sima-land.ru/'.ltrim($product['itemUrl'], '/'),
                'external_id' => $product['id'],
            ];
            if ($item['supply_period']) {
                $data['availability'] = ProductAvailabilityCode::ON_DEMAND;
            }
            else {
                $data['availability'] = ProductAvailabilityCode::AVAILABLE;
            }
            if (!empty($product['photos'])) {
                $data['image_urls'] = array_map(function($photo) {
                    return $photo['url_part'].'700-nw.jpg';
                }, $product['photos']);
            }

            $loader->prepare($data);
        }
        $q = $em->getConnection()->prepare("
            UPDATE supplier_product 
            SET product_availability_code = :out_of_stock
            WHERE supplier_id = :supplier_id AND external_id > :id_greater_than AND external_id <= :last_id
        ");
        $q->execute([
            'supplier_id' => $supplier->getId(),
            'id_greater_than' => $idGreaterThan,
            'last_id' => $counter->getValue(),
            'out_of_stock' => ProductAvailabilityCode::OUT_OF_STOCK,
        ]);

        $loader->flush();
        $logger->info(sprintf('Обновлено %d товаров за %0.3f сек', $loader->uploadedQuantity(), microtime(true) - $s));
        
        $q = $em->getConnection()->prepare("
            INSERT INTO supplier_product_update (supplier_id, min_external_id, max_external_id)
            VALUES (:supplier_id, :min_external_id, :max_external_id)
        ");
        $q->execute([
            'supplier_id' => $supplier->getId(),
            'min_external_id' => $idGreaterThan,
            'max_external_id' => $counter->getValue(),
        ]);
    }

    /**
     * @param array<integer> $externalIds
     */
    protected function updateCategories(Supplier $supplier, array $externalIds)
    {
        $this->loadCategories($supplier, $externalIds);

        $newCategories = [];
        $newExternalIds = [];
        foreach ($externalIds as $externalId) {
            $category = $this->query(sprintf('https://www.sima-land.ru/api/v3/category/%d/', $externalId));
            if (empty($this->categories[$externalId]) || $this->categories[$externalId]['path'] != $category['path']) {
                $parentIds = explode('.', $category['path']);
                if (1 < count($parentIds)) {
                    $newExternalIds[] = array_reverse($parentIds)[1]; 
                }
                $newCategories[$externalId] = $category + ['external_pid' => null, 'pid' => null];
            }  
            echo '.'; 
        }

        if (!empty($newExternalIds)) {
            $this->updateCategories($supplier, $newExternalIds);
            $this->loadCategories($supplier, $newExternalIds);
            foreach ($newCategories as $externalId => $category) {
                $newCategories[$externalId]['pid'] = $this->categories[$category['external_pid']]['pid'];
            }
        }

        if (!empty($newCategories)) {
            $q = $this->getDoctrine()->getManager()->getConnection()->prepare("
                UPDATE supplier_category 
                SET external_id = NULL 
                WHERE external_id IN (".str_repeat('?,', count($newCategories) - 1)."?)
            ");
            $q->execute(array_keys($newCategories));

            $parameters = [];
            foreach ($newCategories as $externalId => $category) {
                $parameters[] = $category['name'];
                $parameters[] = $supplier->getId();
                $parameters[] = $category['pid'];
                $parameters[] = $externalId;
            }
            $placeholders = implode(',', array_fill(0, count($parameters) / 4, '(?::text, ?::integer, ?::integer, ?::integer)'));

            $q = $this->getDoctrine()->getManager()->getConnection()->prepare("
                WITH 
                    data (name, supplier_id, pid, external_id) AS (
                        VALUES {$placeholders}
                    ),
                    updated AS (
                        UPDATE supplier_category 
                        SET 
                            external_id = data.external_id
                        FROM data
                        WHERE data.name = supplier_category.name 
                            AND data.supplier_id = supplier_category.supplier_id
                            AND data.pid = supplier_category.pid 
                        RETURNING *
                    )
                INSERT INTO supplier_category (name, supplier_id, pid, external_id)
                SELECT 
                    data.name,
                    data.supplier_id,
                    data.pid,
                    data.external_id
                FROM data 
                WHERE NOT EXISTS (SELECT 1 FROM updated)
            ");
            $q->execute($parameters);
        }
    }

    protected function loadCategories(Supplier $supplier, array $externalIds)
    {
        $q = $this->getDoctrine()->getManager()->getConnection()->prepare("
            SELECT
                sc.external_id,
                sc.id,
                sc.name,
                sc.pid,
                array_to_string(array_agg(sc2.external_id ORDER BY scp.plevel), '.') path
            FROM supplier_category sc 
            INNER JOIN supplier_category_path scp ON scp.id = sc.pid
            INNER JOIN supplier_category sc2 ON sc2.id = scp.pid   
            WHERE sc.external_id IN (".str_repeat('?,', count($externalIds) - 1)."?) AND sc.supplier_id = ?
            GROUP BY sc.id
        ");
        $q->execute(array_merge($externalIds, [$supplier->getId()]));
        $this->categories += $q->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_GROUP);   
    }
    
    protected function query($url, array $parameters = null)
    {
        if (!empty($parameters)) {
            $url .= '?'.http_build_query($parameters);
        }
        try {
            $response = $this->get('buzz')->get($url);
            if (!$response->isOk()) {
                echo sprintf('Request error, status code %d', $response->getStatusCode()).PHP_EOL;
                sleep(1);

                return $this->query($url);
            }

            return json_decode($response->getContent(), true);
        }
        catch (RequestException $e) {
            echo sprintf('Request error: %d', $e->getMessage()).PHP_EOL;
            sleep(1);
            return $this->query($url);
        }
    }

    protected function markdownReasonIsDisabled($value)
    {
        $em = $this->getDoctrine()->getManager();

        if (null === $this->markdownReasons) {
            $q = $em->createQuery("
                SELECT 
                    mr.hash,
                    mr.isDisabled
                FROM ContentBundle:SMLMarkdownReason mr 
            ");
            $this->markdownReasons = $q->getResult('IndexHydrator');
        }
        $hash = md5(strtolower($value));
        if (!isset($this->markdownReasons[$hash])) {
            $reason = new SMLMarkdownReason();
            $reason->setHash($hash);
            $reason->setValue($value);
            $reason->setIsDisabled(true);
            $reason->setIsVerified(false);
            $em->persist($reason);

            $this->markdownReasons[$hash] = true;
        }

        return $this->markdownReasons[$hash];
    }

    protected function clearDescription($description)
    {
        if (preg_match_all("/<a[^<>]*href=(?:'|\")(.*)(?:'|\")[^<>]*>(.*)<\/a>/iU", $description, $links)) {
            $search = $replace = [];
            foreach ($links[1] as $index => $link) {
                if (preg_match("/http:\/\/(www\.){0,1}vseinet.ru/i", $link)) {
                    if (false === strpos($link, 'http:')) {
                        $search[] = $link;
                        $replace[] = 'http://'.$link;
                    }
                    continue;
                }
                if (preg_match("/(http:|https:)?\/\/(www\.){0,1}(.*)/i", $link)) {
                    $search[] = $links[0][$index];
                    $replace[] = $links[2][$index];
                }
            }
            if (!empty($search)) {
                $description = str_replace($search, $replace, $description);
            }
        }

        return str_ireplace(['sima-land.ru', 'Сима-ленд'], ['vseinet.ru', 'Vseinet.ru'], $description);
    }
} 