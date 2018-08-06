<?php 

namespace ContentBundle\Bus\Template\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetParserSourcesQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение categoryId не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Категория сайта")
     */
    public $categoryId;
}
