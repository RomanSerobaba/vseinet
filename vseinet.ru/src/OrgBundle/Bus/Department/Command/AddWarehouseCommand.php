<?php

namespace OrgBundle\Bus\Department\Command;

use AppBundle\Annotation as VIA;
use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class AddWarehouseCommand extends Message
{
    /**
     * @VIA\Description("Department id")
     * @Assert\Type(type="integer")
     * @Assert\GreaterThan(0)
     * @Assert\NotBlank(
     *     message="Department id should not be blank."
     * )
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @Assert\Choice({"office", "shop", "warehouse"})
     * @Assert\NotBlank()
     */
    public $type;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}