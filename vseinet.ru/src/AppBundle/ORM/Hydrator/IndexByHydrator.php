<?php 
namespace AppBundle\ORM\Hydrator;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class IndexByHydrator extends AbstractHydrator 
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
        $id = [];
        $nonemptyComponents = array();
        $rowData = $this->gatherRowData($row, $id, $nonemptyComponents);
        
        if (isset($rowData['newObjects'])) {
            foreach ($rowData['newObjects'] as $objIndex => $newObject) {
                $class  = $newObject['class'];
                $args   = $newObject['args'];
                $obj    = $class->newInstanceArgs($args);
                $result[reset($args)] = $obj;
            }
        }
    }
}