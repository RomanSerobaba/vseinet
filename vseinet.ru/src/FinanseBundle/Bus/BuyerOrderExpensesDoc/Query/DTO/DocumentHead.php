<?php

namespace FinanseBundle\Bus\BuyerOrderExpensesDoc\Query\DTO;

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
     * @VIA\Description("Наиемнование подразделения")
     * @Assert\Type(type="string")
     */
    public $orgDepartmentName;

    /**
     * @VIA\Description("Идентификатор плательщика")
     * @Assert\Type(type="integer")
     */
    public $financialCounteragentId;

    /**
     * @VIA\Description("Наименование плательщика")
     * @Assert\Type(type="string")
     */
    public $financialCounteragentName;

    /**
     * @VIA\Description("Сумма выставленного счета")
     * @Assert\Type(type="integer")
     */
    public $amount;

    /**
     * @VIA\Description("Идентификатор статьи расхода, на которую будут оприходованы средства")
     * @Assert\Type(type="integer")
     */
    public $toItemOfExpensesId;

    /**
     * @VIA\Description("Наименование статьи расхода, на которую будут оприходованы средства")
     * @Assert\Type(type="string")
     */
    public $toItemOfExpensesName;

    /**
     * @VIA\Description("Дата выставления счета")
     * @Assert\DateTime
     * @Assert\Type(type="datetime")
     */
    public $expectedDateExecute;

    /**
     * @VIA\Description("Ожидаемая дата оплаты")
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
     * @VIA\Description("Идентификатор источника финансов, на который буду оприходованы средства")
     * @Assert\Type(type="integer")
     */
    public $toFinancialResourceId;

    /**
     * @VIA\Description("Наименование источника финансов, на который буду оприходованы средства")
     * @Assert\Type(type="string")
     */
    public $toFinancialResourceName;

    /**
     * @VIA\Description("Дата одобрения расхода")
     * @Assert\DateTime
     * @Assert\Type(type="datetime")
     */
    public $acceptedAt;

    /**
     * @VIA\Description("Дата запрета расхода")
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

}
