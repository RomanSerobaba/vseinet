<?php
namespace ReservesBundle\Bus\GoodsIssueDoc\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

/**
 * Description of DocumentStatus
 *
 * @author denis
 */
class SimpleData {

    /**
     * @VIA\Description("Идентификатор")
     * @Assert\Type(type="integer")
     */
    public $id;
    
    /**
     * @VIA\Description("Наименование")
     * @Assert\Type(type="string")
     */
    public $name;
    
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    
}
