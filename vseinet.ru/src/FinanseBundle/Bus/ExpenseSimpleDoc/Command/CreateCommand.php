<?php 

namespace FinanseBundle\Bus\ExpenseSimpleDoc\Command;

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
     * @Assert\Choice({"new", "active", "rejected", "completed"}, strict=true, multiple=false)
     * @VIA\DefaultValue("new")
     */
    public $statusCode;
    
    /////////////////////////////////////////////
    
   /**
    * @VIA\Description("Идентификатор представительства")
    * @Assert\Type(type="integer")
    */
    public $orgDepartmentId;

   /**
    * @VIA\Description("Идентификатор оборудования")
    * @Assert\Type(type="integer")
    */
    public $equipmentId;

   /**
    * @VIA\Description("Сумма расхода")
    * @Assert\NotNull(message="Сумма операции должна быть указана")
    * @Assert\Type(type="integer")
    */
    public $amount;

    /**
     * @VIA\Description("Идентификатор статьи расхода")
     * @Assert\NotBlank(message="Статья расхода должна быть указана")
     * @Assert\Type(type="integer")
     */
    public $itemOfExpensesId;
            
   /**
    * @VIA\Description("Дата исполнения расхода")
    * @Assert\NotNull(message="Дата исполнения расхода должна быть указана")
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
     * @Assert\Uuid
     */
    public $uuid;
    
}