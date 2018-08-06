<?php

namespace FinanseBundle\Bus\BuyerOrderExpensesDoc\Command;

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
     * @Assert\Choice({"new", "active", "rejected", "completed"}, strict=true, multiple=false)
     */
    public $statusCode;

    /////////////////////////////////////////////

    /**
     * @VIA\Description("Идентификатор подразделения")
     * @Assert\Type(type="integer")
     */
    public $orgDepartmentId;

    /**
     * @VIA\Description("Идентификатор подотчетного лица")
     * @Assert\NotNull(message="Подотчестное лицо должно быть указано")
     * @Assert\Type(type="integer")
     */
    public $financialCounteragentId;

    /**
     * @VIA\Description("Сумма расхода")
     * @Assert\NotNull(message="Сумма операции должна быть указана")
     * @Assert\Type(type="integer")
     */
    public $amount;

    /**
     * @VIA\Description("Идентификатор статьи расхода, на которую будут списаны выданные средства")
     * @Assert\NotBlank(message="Статья расхода должна быть указана")
     * @Assert\Type(type="integer")
     */
    public $toItemOfExpensesId;

    /**
     * @VIA\Description("Дата исполнения расхода")
     * @Assert\NotNull(message="Дата исполнения расхода должна быть указана")
     * @Assert\Type(type="datetime")
     */
    public $expectedDateExecute;

    /**
     * @VIA\Description("Ожидаемая дата отчета по выданным средствам")
     * @Assert\Type(type="datetime")
     */
    public $maturityDateExecute;

    /**
     * @VIA\Description("Описание расхода")
     * @Assert\Type(type="string")
     */
    public $description;

    /**
     * @VIA\Description("Идентификатор источника финансов")
     * @Assert\Type(type="integer")
     */
    public $toFinancialResourceId;

}