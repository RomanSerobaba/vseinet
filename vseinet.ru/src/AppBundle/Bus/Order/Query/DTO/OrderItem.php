<?php 

namespace OrderBundle\Bus\Data\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;
use AppBundle\Validator\Constraints\Enum;
use AppBundle\Enum\OrderItemStatusCode;

class OrderItem
{
    /**
     * @VIA\Description("Идентификатор позиции")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Идентификатор заказа")
     * @Assert\Type(type="integer")
     */
    public $orderId;

    /**
     * @VIA\Description("Количество товаров")
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @VIA\Description("Код статуса позиции")
     * @Enum("AppBundle\Enum\OrderItemStatus")
     */
    public $statusCode;

    /**
     * @VIA\Description("Заголовок статуса позиции")
     */
    public $statusTitle;

    /**
     * @VIA\Description("Наименование товара")
     * @Assert\Type(type="string")
     */
    public $productName;

    /**
     * @VIA\Description("Идентификатор товара")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @VIA\Description("Количество комментариев")
     * @Assert\Type(type="integer")
     */
    public $commentsCount;

    /**
     * @VIA\Description("Цена продажи")
     * @Assert\Type(type="integer")
     */
    public $retailPrice;

    /**
     * @VIA\Description("Цена закупки")
     * @Assert\Type(type="integer")
     */
    public $purchasePrice;

    /**
     * @VIA\Description("Код поставщика")
     * @Assert\Type(type="string")
     */
    public $supplierCode;

    /**
     * @VIA\Description("Ожидаемая дата прихода")
     * @Assert\Type(type="date")
     */
    public $deliveryDate;

    /**
     * @VIA\Description("Ф.И.О. менеджера")
     * @Assert\Type(type="string")
     */
    public $managerName;

    /**
     * @VIA\Description("Количество доступных резервов в наличии")
     * @Assert\Type(type="integer")
     */
    public $reservesCount;

    /**
     * @VIA\Description("Отфильтрованная позиция")
     * @Assert\Type(type="boolean")
     */
    public $isFiltered;

    /**
     * @VIA\Description("Размер текущей предоплаты")
     * @Assert\Type(type="integer")
     */
    public $prepaymentAmount;
    
    /**
     * @VIA\Description("Размер требуемой предоплаты")
     * @Assert\Type(type="integer")
     */
    public $requiredPrepayment;

    /**
     * OrderItem constructor.
     * @param $id
     * @param $orderId
     * @param $quantity
     * @param $statusCode
     * @param $productName
     * @param $baseProductId
     * @param $commentsCount
     * @param $retailPrice
     * @param $purchasePrice
     * @param $supplierCode
     * @param $deliveryDate
     * @param $managerName
     * @param $reservesCount
     * @param $isFiltered
     * @param $prepaymentAmount
     * @param $requiredPrepayment
     */
    public function __construct($id, $orderId, $quantity, $statusCode, $productName, $baseProductId, $commentsCount, $retailPrice, $purchasePrice, $supplierCode, $deliveryDate, $managerName, $reservesCount, $isFiltered = false, $prepaymentAmount = 0, $requiredPrepayment = 0)
    {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->quantity = $quantity;
        $this->statusCode = $statusCode;
        $this->productName = $productName;
        $this->baseProductId = $baseProductId;
        $this->commentsCount = $commentsCount;
        $this->retailPrice = $retailPrice;
        $this->purchasePrice = $purchasePrice;
        $this->supplierCode = $supplierCode;
        $this->deliveryDate = $deliveryDate;
        $this->managerName = $managerName;
        $this->reservesCount = $reservesCount;
        $this->isFiltered = $isFiltered;
        $this->prepaymentAmount = $prepaymentAmount;
        $this->requiredPrepayment = $requiredPrepayment;
    }
}