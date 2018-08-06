<?php 

namespace ContentBundle\Bus\Parser\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetTemplateQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение categoryId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\Choice({"all", "active", "hidden"}, strict=true)
     * @VIA\DefaultValue("all")
     */
    public $filter;
}