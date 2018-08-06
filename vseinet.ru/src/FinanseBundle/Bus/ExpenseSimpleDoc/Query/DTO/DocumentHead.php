<?php

namespace FinanseBundle\Bus\ExpenseSimpleDoc\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DocumentHead extends DocumentList
{
    use \DocumentBundle\Prototipe\DocumentDTO;

    /**
     * @VIA\Description("Идентификатор подразделения")
     * @Assert\Type(type="integer")
     */
    public $orgDepartmentId;

    /**
     * @VIA\Description("Наименование подразделения")
     * @Assert\Type(type="string")
     */
    public $orgDepartmentName;

    /**
     * @VIA\Description("Сумма расхода")
     * @Assert\Type(type="integer")
     */
    public $amount;

    /**
     * @VIA\Description("Идентификатор статьи расхода")
     * @Assert\Type(type="integer")
     */
    public $itemOfExpensesId;

    /**
     * @VIA\Description("Наименование статьи расхода")
     * @Assert\Type(type="string")
     */
    public $itemOfExpensesName;

    /**
     * @VIA\Description("Идентификатор оборудования")
     * @Assert\Type(type="integer")
     */
    public $equipmentId;

    /**
     * @VIA\Description("Наименование оборудования")
     * @Assert\Type(type="string")
     */
    public $equipmentName;

    /**
     * @VIA\Description("Ожидаемая дата выполнения расхода")
     * @Assert\DateTime
     * @Assert\Type(type="datetime")
     */
    public $expectedDateExecute;

    /**
     * @VIA\Description("Ожидаемая дата отчета по выданным средствам")
     * @Assert\DateTime
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
    public $financialResourceId;

    /**
     * @VIA\Description("Наименование источника финансов")
     * @Assert\Type(type="string")
     */
    public $financialResourceName;

    /**
     * @VIA\Description("Время одобрения расхода")
     * @Assert\DateTime
     * @Assert\Type(type="datetime")
     */
    public $acceptedAt;

    /**
     * @VIA\Description("Время запрета расхода")
     * @Assert\DateTime
     * @Assert\Type(type="datetime")
     */
    public $rejectedAt;

    /**
     * @VIA\Description("Идентификатор пользователя одобрившего расход")
     * @Assert\Type(type="integer")
     */
    public $acceptedBy;

    /**
     * @VIA\Description("Наименование пользователя одобрившего расход")
     * @Assert\Type(type="string")
     */
    public $acceptedName;

    /**
     * @VIA\Description("Идентификатор пользователя отклонившего расход")
     * @Assert\Type(type="integer")
     */
    public $rejectedBy;

    /**
     * @VIA\Description("Наиемнование пользователя отклонившего расход")
     * @Assert\Type(type="string")
     */
    public $rejectedName;

}
