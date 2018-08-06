<?php

namespace DocumentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentStatus
 * @ORM\Table(name="any_doc")
 * @ORM\Entity()
*/
class AnyDoc
{
    use \DocumentBundle\Prototipe\DocumentEntity;
    
    /**
     * @var integer
     * @ORM\Column(name="tableoid", type="integer")
     */
    private $tableOID;
    
    /**
     * Получить идентификатор таблицы
     * @return int
     */
    public function getTableOID()
    {
        return $this->tableOID;
    }

}
