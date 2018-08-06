<?php 

namespace ContentBundle\Bus\Category\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("Включить в ответ хлебные крошки")
     * @VIA\DefaultValue(true)
     */
    public $breadcrumbs;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("Включить в ответ SEO: description, pageTitle, pageDescription")
     * @VIA\DefaultValue(false)
     */
    public $seo;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Включить в ответ SEO по бренду")
     * @VIA\DefaultValue(null)
     */
    public $brandId;
}