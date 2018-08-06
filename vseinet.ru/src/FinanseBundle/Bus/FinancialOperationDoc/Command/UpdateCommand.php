<?php

namespace FinanseBundle\Bus\FinancialOperationDoc\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateCommand extends Message
{
    /**
     * @VIA\Description("Уникальный идентификатор изменяемого документа")
     * @Assert\NotBlank(message="Идентификатор изменяемого документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Человекочитаемый заголовок")
     * @Assert\NotBlank(message="Заголовок изменяемого документа должен быть указан")
     * @Assert\Type(type="string")
     */
    public $title;

    /**
     * @VIA\Description("Статус документа")
     * @Assert\NotBlank(message="Статус изменяемого документа должен быть указан")
     * @Assert\Choice({"new", "completed"}, strict=true, multiple=false)
     */
    public $statusCode;

    /**
     * @VIA\Description("Список прикреплённых документов")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="FinanseBundle\Bus\BankOperationDoc\Command\Schema\RelatedDocument"))
     */
    public $relatedDocuments;

    /**
     * @VIA\Description("Финансовый контрагент")
     * @Assert\Type(type="integer")
     */
    public $financialCounteragent;

}