<?php

namespace FinanseBundle\Bus\AccountableExpensesDoc\Query\DTO;

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
     * @VIA\Description("Наменовнаие подразделения")
     * @Assert\Type(type="string")
     */
    public $orgDepartmentName;

    /**
     * @VIA\Description("Идентификатор получателя")
     * @Assert\Type(type="integer")
     */
    public $financialCounteragentId;

    /**
     * @VIA\Description("Наменовнаие получателя")
     * @Assert\Type(type="string")
     */
    public $financialCounteragentName;

    /**
     * @VIA\Description("Идентификатор оборудования")
     * @Assert\Type(type="integer")
     */
    public $toEquipmentId;

    /**
     * @VIA\Description("Заголовок оборудования")
     * @Assert\Type(type="string")
     */
    public $toEquipmentName;

    /**
     * @VIA\Description("Сумма расхода")
     * @Assert\Type(type="integer")
     */
    public $amount;

    /**
     * @VIA\Description("Идентификатор статьи расхода")
     * @Assert\Type(type="integer")
     */
    public $toItemOfExpensesId;

    /**
     * @VIA\Description("Наиемновнаие статьи расхода")
     * @Assert\Type(type="string")
     */
    public $toItemOfExpensesName;

    /**
     * @VIA\Description("Ожидаемая дата выполнения расхода")
     * @Assert\DateTime
     * @Assert\Type(type="datetime")
     */
    public $expectedDateExecute;

    /**
     * @VIA\Description("Ожидаемая дата отчета по расходам")
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
     * @VIA\Description("Наменовнаие источника финансов")
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
     * @VIA\Description("Наименование пользователя отклонившего расход")
     * @Assert\Type(type="string")
     */
    public $rejectedName;

    /**
     * @VIA\Description("Время выдачи средств")
     * @Assert\DateTime
     * @Assert\Type(type="datetime")
     */
    public $paymentAt;

    /**
     * @VIA\Description("Идентификатор пользователя выдавшего средства")
     * @Assert\Type(type="integer")
     */
    public $paymentBy;

    /**
     * @VIA\Description("Наименование пользователя выдавшего средства")
     * @Assert\Type(type="string")
     */
    public $paymentName;

}
