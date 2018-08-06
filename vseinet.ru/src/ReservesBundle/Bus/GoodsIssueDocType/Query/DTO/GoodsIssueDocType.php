<?php 
namespace ReservesBundle\Bus\GoodsIssueDocType\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;
use AppBundle\SimpleTools\DocumentTools;

class GoodsIssueDocType
{

    /**
     * @VIA\Description("Идентификатор типа претензии")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Тип претензии доступен к использованию")
     * @Assert\Type(type="boolean")
     */
    public $isActive;

    /**
     * @VIA\Description("Тип претензии доступен к интерактивному использованию")
     * @Assert\Type(type="boolean")
     */
    public $isInteractive;
    
    /**
     * @VIA\Description("Наименование типа претензии")
     * @Assert\Type(type="string")
     */
    public $name;
    
    /**
     * @VIA\Description("Список допустимых видов состояний товара")
     * @Assert\Type(type="array")
     */
    public $availableGoodsStates;
    
    /**
     * @VIA\Description("Претензия по товару")
     * @Assert\Type(type="boolean")
     */
    public $byGoods;

    /**
     * @VIA\Description("Претензия по покупателю")
     * @Assert\Type(type="boolean")
     */
    public $byClient;

    /**
     * @VIA\Description("Претензия по поставщику")
     * @Assert\Type(type="boolean")
     */
    public $bySupplier;

    public function __construct(int $id, bool $isActive, bool $isInteractive, string $name, $availableGoodsStates, bool $byGoods, bool $byClient, bool $bySupplier)
    {
        if (empty($availableGoodsStates)) {
            $truAvailableGoodsStates = [];
        }else{
            $truAvailableGoodsStates = explode(',', str_replace(['{','}'], '', $availableGoodsStates));
        }
        
        $this->id = $id;
        $this->isActive = $isActive;
        $this->isInteractive = $isInteractive;
        $this->name = $name;
        $this->availableGoodsStates = $truAvailableGoodsStates;
        $this->byGoods = $byGoods;
        $this->byClient = $byClient;
        $this->bySupplier = $bySupplier;
    }
    
}