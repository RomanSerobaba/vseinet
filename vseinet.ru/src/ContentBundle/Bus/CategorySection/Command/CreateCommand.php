<?php 

namespace ContentBundle\Bus\CategorySection\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Не указан код категории")
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\NotBlank(message="Наименование раздела катеогии не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\NotBlank(message="Базовое наименование товаров в разделе категории не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $basename;

    /**
     * @Assert\Choice({"male", "female", "neuter", "plural"}, strict=true)
     */
    public $gender;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}