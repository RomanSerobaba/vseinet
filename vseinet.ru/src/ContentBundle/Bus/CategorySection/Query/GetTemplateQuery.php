<?php 

namespace ContentBundle\Bus\CategorySection\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetTemplateQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Не указан код категории")
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\NotBlank(message="Не указан код раздела категории")
     * @Assert\Type(type="integer")
     * @VIA\Description("Для получения общих характеристик передать 0")
     */
    public $id;
}