<?php

namespace FinanseBundle\Bus\BankOperationDoc\Command;

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
     * @Assert\Choice({"new", "completed"}, strict=true, multiple=false)
     * @VIA\DefaultValue("new")
     */
    public $statusCode;

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
     * @VIA\Description("Сумма зачтенного аванса")
     * @Assert\Type(type="integer")
     */
    public $advancePayment;
    
    /**
     * @VIA\Description("Список прикреплённых документов")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="FinanseBundle\Bus\BankOperationDoc\Command\Schema\RelatedDocument"))
     */
    public $relatedDocuments;
    
    /**
     * @Assert\Uuid
     */
    public $uuid;

}