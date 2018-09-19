<?php 
namespace AppBundle\Doctrine\ORM\Hydrator;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class ListAssocHydrator extends AbstractHydrator
{     
    protected function hydrateAllData()     
    {         
        $result = [];   

        while ($row = $this->_stmt->fetch(\PDO::FETCH_ASSOC)) {
            $this->hydrateRowData($row, $result);
        }

        return $result;
    }

    protected function hydrateRowData(array $row, array &$result)
    {
        $result[] = $row;
    }
}