<?php

namespace FinanseBundle\Bus\FinancialOperations\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use DocumentBundle\Prototipe\ParentDocumentDTO;
use DocumentBundle\SimpleTools\DocumentNameConverter;
use AppBundle\Annotation as VIA;

class Documents
{
    use \DocumentBundle\Prototipe\DocumentDTO;

    /**
     * @VIA\Description("Тип родительского документа")
     * @Assert\Type(type="string")
     */
    public $documentType;

    /**
     * @VIA\Description("Идентификатор города")
     * @Assert\Type(type="integer")
     */
    public $cityId;

    /**
     * @VIA\Description("Наименование города")
     * @Assert\Type(type="string")
     */
    public $cityName;

    /**
     * @VIA\Description("Общая сумма операции")
     * @Assert\Type(type="integer")
     */
    public $totalAmount;

    /**
     * @VIA\Description("Идентификатор источника финансов")
     * @Assert\Type(type="integer")
     */
    public $financialResourcesId;

    /**
     * @VIA\Description("Наименование источника финансов")
     * @Assert\Type(type="string")
     */
    public $financialResourcesName;

    /**
     * @VIA\Description("Суммы по типам платежей")
     * @Assert\Type(type="array<FinanseBundle\Bus\FinancialOperations\Query\DTO\AmountsByCode>")
     */
    public $amountsByCodes;

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
     * @VIA\Description("Связаные документы")
     * @Assert\Type(type="array<DocumentBundle\Prototipe\ParentDocumentDTO>")
     */
    public $relatedDocuments;

    /////////////////////////////////////////////////////////

    public function setAmountsByCodes($inJson)
    {
        $this->amountsByCodes = [];
        $inData = json_decode($inJson, true);
        if (!empty($inData)) {
            foreach ($inData as $value) {
                $this->amountsByCodes[] = new AmountsByCode($value['payCode'], $value['amount']);
            }
        }

    }

    public function setRelatedDocuments($inJson)
    {

        $this->relatedDocuments = [];
        $inData = json_decode($inJson, true);
        if (!empty($inData)) {
            foreach ($inData as $value) {
                $this->relatedDocuments[] = new ParentDocumentDTO($inData['id'], $inData['title'], $inData['document_type']);
            }
        }

    }

    public function setDocumentType(string $documentType)
    {
        $this->documentType = DocumentNameConverter::TableName2Type($documentType);
//        $this->documentType = $documentType;
    }

}