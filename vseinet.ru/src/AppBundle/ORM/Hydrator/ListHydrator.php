<?php 
namespace AppBundle\ORM\Hydrator;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class ListHydrator extends AbstractHydrator 
{     
    protected function hydrateAllData()     
    {         
        $result = [];   

        while ($row = $this->_stmt->fetch(\PDO::FETCH_NUM)) {
            $this->hydrateRowData($row, $result);
        }

        return $result;
    }

    protected function hydrateRowData(array $row, array &$result)
    {
        // Assume first column is id field
        $id = array_shift($row);

        switch (count($row)) {
            case 0:
                $result[] = $id;
                break;

            case 1:
                // If only one more field assume that this is the value field
                $result[$id] = $row[0];
                break;

            default:
                $result[$id] = $row;
        }
    }
}