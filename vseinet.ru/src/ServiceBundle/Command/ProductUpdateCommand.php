<?php

namespace ServiceBundle\Command;

use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ProductUpdateCommand extends ContainerAwareCommand
{
    const QUEUE_LIMIT = 10;
    const QUEUE_TTL = 20 * 60; // 20 минут

    /**
     * Entity Manager
     *
     * @var EntityManager
     */
    private $_em;

    /**
     * @param EntityManager $em
     */
    public function setEm(EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
     * @return EntityManager
     */
    public function getEm() : EntityManager
    {
        return $this->_em;
    }

    protected function configure()
    {
        $this->setDescription('Product update command')->setName('product:update');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setEm($this->getContainer()->get('doctrine.orm.entity_manager'));
        $service = $this->getContainer()->get('service.product');

        echo 'Обновление продуктов...'.PHP_EOL;

        $queue = $this->_getBaseProductIds();

        echo 'Очередь: '.count($queue).PHP_EOL;

        if (empty($queue)) {
            exit(0);
        }

        try {
            $this->_setReceived($queue);

            $updatedIds = $service->update(array_keys($queue));

            if ($updatedIds) {
                $this->_updateIndex($updatedIds);
            }

            $this->_checkQueue();
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    private function _getBaseProductIds() : array
    {
        $result = [];

        $query = $this->getEm()->createNativeQuery('
            SELECT
                id,
                base_product_id,
                received_at
            FROM
                product_update_queue
            WHERE
                received_at IS NULL
            ORDER BY 
                created_at ASC
            LIMIT '.self::QUEUE_LIMIT.'
        ', new ResultSetMapping());

        $rows = $query->getResult('ListAssocHydrator');

        foreach ($rows as $row) {
            $result[$row['base_product_id']] = $row;
        }

        return $result;
    }

    /**
     * @param array $queue
     */
    private function _setReceived(array $queue) : void
    {
        $ids = [];

        foreach ($queue as $item) {
            $ids[] = $item['id'];
        }

        if ($ids) {
            $sql = 'UPDATE product_update_queue SET received_at = NOW() WHERE id IN ('.implode(',', $ids).')';

            $statement = $this->getEm()->getConnection()->prepare($sql);
            if (!$statement->execute()) {
                echo '[ERROR] Ошибка обновления даты получения в очереди!'.PHP_EOL;
            } else {
                echo 'Очередь ('.count($ids).' строк) обновлена...'.PHP_EOL;
            }
        }
    }

    private function _checkQueue() : void
    {
        echo 'Проверка зависших очередей...'.PHP_EOL;

        $ids = [];

        $query = $this->getEm()->createNativeQuery('
            SELECT
                id
            FROM
                product_update_queue
            WHERE
                received_at IS NOT NULL
                AND EXTRACT(EPOCH FROM (NOW() - received_at)) > :ttl::INTEGER
            ORDER BY 
                created_at ASC
        ', new ResultSetMapping());
        $query->setParameter('ttl', self::QUEUE_TTL);

        $rows = $query->getResult('ListAssocHydrator');

        foreach ($rows as $row) {
            $ids[] = $row['id'];
        }

        if ($ids) {
            $sql = 'UPDATE product_update_queue SET received_at = NULL WHERE id IN ('.implode(',', $ids).')';

            $statement = $this->getEm()->getConnection()->prepare($sql);
            if (!$statement->execute()) {
                echo '[ERROR] Ошибка сброса даты получения в очереди!'.PHP_EOL;
            } else {
                echo 'Зависшая очередь ('.count($ids).' строк) обновлена...'.PHP_EOL;
            }
        } else {
            echo 'Не обнаружены...'.PHP_EOL;
        }
    }

    /**
     * @param array $baseProductIds
     */
    private function _cleanQueue(array $baseProductIds) : void
    {
        $sql = 'DELETE FROM product_update_queue WHERE received_at IS NOT NULL AND base_product_id IN ('.implode(',', $baseProductIds).')';

        $statement = $this->getEm()->getConnection()->prepare($sql);
        if (!$statement->execute()) {
            echo '[ERROR] Ошибка удаления данных из очереди!'.PHP_EOL;
        } else {
            echo 'Очередь ('.count($baseProductIds).' строк) очищена...'.PHP_EOL;
        }
    }

    /**
     * @param array $baseProductIds
     */
    private function _updateIndex(array $baseProductIds) : void
    {
        // вызов Сфинкс сервиса для обновления
        echo 'Update sphinx filter...'.PHP_EOL;

        $this->_cleanQueue($baseProductIds);
    }
}
