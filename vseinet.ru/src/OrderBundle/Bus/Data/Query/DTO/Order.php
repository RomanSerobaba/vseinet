<?php 

namespace OrderBundle\Bus\Data\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class Order
{
    /**
     * @VIA\Description("Идентификатор")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Город")
     * @Assert\Type(type="string")
     */
    public $cityName;

    /**
     * @VIA\Description("Идентификатор клиента")
     * @Assert\Type(type="string")
     */
    public $userId;

    /**
     * @VIA\Description("Ф.И.О. клиента")
     * @Assert\Type(type="string")
     */
    public $userName;

    /**
     * @VIA\Description("Аббревиатура типа оплаты")
     * @Assert\Type(type="string")
     */
    public $paymentTypeAbbreviation;

    /**
     * @VIA\Description("Тип оплаты")
     * @Assert\Type(type="string")
     */
    public $paymentTypeName;

    /**
     * @VIA\Description("Менеджер")
     * @Assert\Type(type="string")
     */
    public $managerName;

    /**
     * @VIA\Description("Позиции")
     * @Assert\Type(type="array<OrderBundle\Bus\Data\Query\DTO\OrderItem>")
     */
    public $items;

    /**
     * @VIA\Description("Дата создания")
     * @Assert\Type(type="datetime")
     */
    public $createdAt;

    /**
     * @VIA\Description("Продающая организация")
     * @Assert\Type(type="string")
     */
    public $sellerCounteragentName;

    /**
     * @VIA\Description("Контакты")
     * @Assert\Type(type="string")
     */
    public $contacts;

    /**
     * @VIA\Description("Количество комментариев")
     * @Assert\Type(type="integer")
     */
    public $commentsCount;

    /**
     * @VIA\Description("Индикатор недозвона до клиента")
     * @Assert\Type(type="boolean")
     */
    public $isNotReached;

    /**
     * @VIA\Description("Сумма заказа")
     * @Assert\Type(type="integer")
     */
    public $orderSum;

    /**
     * @VIA\Description("Отфильтрованные позиции")
     * @Assert\Type(type="string")
     */
    public $itemsIds;

    /**
     * @VIA\Description("Размер текущей предоплаты")
     * @Assert\Type(type="integer")
     */
    public $prepaymentAmount;

    /**
     * Order constructor.
     * @param $id
     * @param $cityName
     * @param $userId
     * @param $userName
     * @param $paymentTypeAbbrev
     * @param $paymentTypeName
     * @param $managerName
     * @param $createdAt
     * @param $sellerCounteragentName
     * @param $commentsCount
     * @param $isNotReached
     * @param $orderSum
     * @param $itemsIds
     */
    public function __construct($id, $cityName, $userId, $userName, $paymentTypeAbbreviation, $paymentTypeName, $managerName, $createdAt, $sellerCounteragentName, $contacts, $commentsCount, $isNotReached, $orderSum, $itemsIds, $prepaymentAmount = 0)
    {
        $this->id = $id;
        $this->cityName = $cityName;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->paymentTypeAbbreviation = $paymentTypeAbbreviation;
        $this->paymentTypeName = $paymentTypeName;
        $this->managerName = $managerName;
        $this->createdAt = $createdAt;
        $this->sellerCounteragentName = $sellerCounteragentName;
        $this->contacts = $contacts;
        $this->commentsCount = $commentsCount;
        $this->isNotReached = $isNotReached;
        $this->orderSum = $orderSum;
        $this->itemsIds = $itemsIds;
        $this->prepaymentAmount = $prepaymentAmount;
    }
}