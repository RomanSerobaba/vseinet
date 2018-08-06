<?php

namespace FinanseBundle\Bus\ExpenseOperations\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use DocumentBundle\Prototipe\ParentDocumentDTO;
use DocumentBundle\SimpleTools\DocumentNameConverter;
use AppBundle\Annotation as VIA;

class ListExpensesDocumentDTO
{

    use \DocumentBundle\Prototipe\DocumentDTO;

    /**
     * @VIA\Description("Тип документа")
     * @Assert\Type(type="string")
     */
    public $documentType;

    /**
     * @VIA\Description("Общая сумма операции")
     * @Assert\Type(type="integer")
     */
    public $amount;

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
     * @VIA\Description("Идентификатор финансового контрагента")
     * @Assert\Type(type="integer")
     */
    public $financialCounteragentId;

    /**
     * @VIA\Description("Наименование финансового конрагента")
     * @Assert\Type(type="string")
     */
    public $financialCounteragentName;

    /**
     * @VIA\Description("Идентификатор оборуования/автомобиля")
     * @Assert\Type(type="integer")
     */
    public $equipmentId;

    /**
     * @VIA\Description("Наименование оборудования/автомобиля")
     * @Assert\Type(type="string")
     */
    public $equipmentName;

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
     * @VIA\Description("Ожидаемая дата выполнения расхода")
     * @Assert\Type(type="datetime")
     */
    public $expectedDateExecute;

    /**
     * @VIA\Description("Ожидаемая дата отчета по выданным средствам")
     * @Assert\Type(type="datetime")
     */
    public $maturityDateExecute;

    /**
     * @VIA\Description("Комментарий/Описание")
     * @Assert\Type(type="string")
     */
    public $description;

    /**
     * @VIA\Description("Время ободрения расхода")
     * @Assert\Type(type="datetime")
     */
    public $acceptedAt;

    /**
     * @VIA\Description("Идентификатор ободрителя расхода")
     * @Assert\Type(type="integer")
     */
    public $acceptedBy;

    /**
     * @VIA\Description("Наименовнаие ободрителя расхода")
     * @Assert\Type(type="string")
     */
    public $acceptedName;

    /**
     * @VIA\Description("Время запрета расходе")
     * @Assert\Type(type="datetime")
     */
    public $rejectedAt;

    /**
     * @VIA\Description("Идентификатор запретителя расхода")
     * @Assert\Type(type="integer")
     */
    public $rejectedBy;

    /**
     * @VIA\Description("Наименовнаие запретителя расхода")
     * @Assert\Type(type="string")
     */
    public $rejectedName;

    /////////////////////////////////////////////////////////

    public function setDocumentType(string $documentType)
    {
        $this->documentType = DocumentNameConverter::TableName2Type($documentType);
    }

}
