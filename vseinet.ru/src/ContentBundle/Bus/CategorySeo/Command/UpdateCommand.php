s<?php 

namespace ContentBundle\Bus\CategorySeo\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Если передан, то SEO для связки категория-бренд, иначе общее для категории")
     */
    public $brandId;

    /**
     * @Assert\Type(type="string")
     */
    public $description;

    /**
     * @Assert\Type(type="string")
     */
    public $pageTitle;

    /**
     * @Assert\Type(type="string")
     */
    public $pageDescription;
}