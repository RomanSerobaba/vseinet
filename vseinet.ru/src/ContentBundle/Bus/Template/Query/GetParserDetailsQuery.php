<?php 

namespace ContentBundle\Bus\Template\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetParserDetailsQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение categoryId не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Категория сайта")
     */
    public $categoryId;
    
    /**
     * @Assert\NotBlank(message="Значение sourceId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $sourceId;

    /**
     * @Assert\NotBlank(message="Значение groupId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $groupId;
}
