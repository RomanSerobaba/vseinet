<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class SetColorCompositeCommand extends Message
{    
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Значение schemaType не должно быть пустым")
     * @Assert\Choice({"single", "transparent", "rainbow", "steel", "in-assortment", "metal", "matte", "pearl"}, strict=true)
     * @VIA\DefaultValue("single")
     */
    public $schemaType;

    /**
     * @Assert\All({
     *     @Assert\Type(type="integer")
     * })
     */
    public $colorIds;

    /**
     * @Assert\Type(type="boolean")
     */
    public $withPicture;

    /**
     * @Assert\Type(type="string")
     */
    public $pictureName;


    public function getColorId($index)
    {
        return isset($this->colorIds[$index - 1]) ? $this->colorIds[$index - 1] : null;
    }
}