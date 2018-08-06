<?php 

namespace ContentBundle\Bus\ColorComposite\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetFormedValueQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение categoryId не долно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\NotBlank(message="Значение schemaType не должно быть пустым")
     * @Assert\Choice({"single", "transparent", "rainbow", "steel", "in-assortment", "metal", "matte", "pearl"}, strict=true)
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
}