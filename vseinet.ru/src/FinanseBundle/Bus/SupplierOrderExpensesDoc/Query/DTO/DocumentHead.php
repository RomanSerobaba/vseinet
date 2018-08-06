<?php

namespace FinanseBundle\Bus\SupplierOrderExpensesDoc\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;
use DocumentBundle\Prototipe\ParentDocumentDTO;

class DocumentHead extends DocumentList
{
    use \DocumentBundle\Prototipe\DocumentDTO;

    /**
     * @VIA\Description("Идентификатор представительства")
     * @Assert\Type(type="integer")
     */
    public $orgDepartmentId;

    /**
     * @VIA\Description("Наименование представительства")
     * @Assert\Type(type="string")
     */
    public $orgDepartmentName;

    /**
     * @VIA\Description("Идентификатор подотчетного лица")
     * @Assert\Type(type="integer")
     */
    public $financialCounteragentId;

    /**
     * @VIA\Description("Наименование подотчетного лица")
     * @Assert\Type(type="string")
     */
    public $financialCounteragentName;

    /**
     * @VIA\Description("Сумма оплаты бонусами")
     * @Assert\Type(type="integer")
     */
    public $amountBonus;

    /**
     * @VIA\Description("Сумма оплаты взаиморасчетом")
     * @Assert\Type(type="integer")
     */
    public $amountMutual;

    /**
     * @VIA\Description("Сумма оплаты из финансового источника")
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
     * @VIA\Description("Ожидаемая дата выполнения расхода")
     * @Assert\DateTime
     * @Assert\Type(type="datetime")
     */
    public $expectedDateExecute;

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
     * @VIA\Description("")
     * @Assert\DateTime
     * @Assert\Type(type="datetime")
     */
    public $acceptedAt;

    /**
     * @VIA\Description("")
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
    public $acceptedByName;

    /**
     * @VIA\Description("Идентификатор пользователя отклонившего расход")
     * @Assert\Type(type="integer")
     */
    public $rejectedBy;

    /**
     * @VIA\Description("Наиемнование пользователя отклонившего расход")
     * @Assert\Type(type="string")
     */
    public $rejectedByName;

    /**
     * @VIA\Description("Список связанных документов")
     * @Assert\Type(type="array<DocumentBundle\Prototipe\ParentDocumentDTO>")
     */
    public $relativeDocuments;

    public function setRelativeDocuments($inJson)
    {
        $this->relativeDocuments = [];

        $inData = json_decode($inJson, true);
        if (!empty($inData) && is_array($inData)) {
            foreach ($inData as $value) {

                $this->relativeDocuments = new ParentDocumentDTO($value['id'], $value['title'], $value['document_type']);
            }
        }
    }

}
