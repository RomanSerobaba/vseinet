<?php

namespace FinanseBundle\Bus\FinancialOperationDoc\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{

    /**
     * @VIA\Description("Универсальный идентификатор документа-родителя")
     * @Assert\Type(type="integer")
     */
    public $parentDocumentId;

    /**
     * @VIA\Description("Человекочитаемый заголовок")
     * @Assert\Type(type="string")
     */
    public $title;

    /**
     * @VIA\Description("Статус документа")
     * @Assert\NotBlank(message="Статус документа должен быть указан")
     * @Assert\Choice({"new", "active", "completed"}, strict=true, multiple=false)
     * @VIA\DefaultValue("new")
     */
    public $statusCode;

    /**
     * @VIA\Description("Операция документа")
     * @Assert\NotBlank(message="Операция документа должна быть указана")
     * @Assert\Choice({"receiving", "sending", "transfer"}, strict=true, multiple=false)
     */
    public $operationCode;

    /**
     * @VIA\Description("Идентификатор источника финансов")
     * @Assert\NotBlank(message="Источник финансов должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $financialResourceId;

    /**
     * @VIA\Description("Финансовый контрагент")
     * @Assert\Type(type="integer")
     */
    public $financialCounteragent;

    /**
     * @VIA\Description("Список прикреплённых документов")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="FinanseBundle\Bus\FinancialOperationDoc\Command\Schema\RelatedDocument"))
     */
    public $relatedDocuments;

   /**
    * @VIA\Description("Сумма платежа")
    * @Assert\NotNull(message="Сумма операции должна быть указана")
    * @Assert\Type(type="integer")
    */
    public $amount;

   /**
     * @Assert\Uuid
     */
    public $uuid;

}