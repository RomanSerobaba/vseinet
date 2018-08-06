<?php 

namespace OrgBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class CreateResupplyOrderCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым") 
     * @VIA\Description("Идентификатор точки")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Для создания заявки необходим список товаров")
     * @VIA\Description("Список товаров")
     * @Assert\Type(type="array")
     * @Assert\All(
     *  @Assert\Callback({"OrgBundle\Bus\Representative\Command\Scheme\ResupplyProduct", "validate"})
     * )
     */
    public $products;
    
    /**
     * @Assert\Uuid
     */
    public $uuid;
}