<?php 

namespace ContentBundle\Bus\Template\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetCategoriesQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение pid не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Для получения первого уровня передать 0")
     */
    public $pid;

    /**
     * @Assert\Choice({"all", "with-tpl", "without-tpl", "manual-tpl", "auto-tpl"}, strict=true)
     * @VIA\DefaultValue("with-tpl")
     */
    public $filter;
}