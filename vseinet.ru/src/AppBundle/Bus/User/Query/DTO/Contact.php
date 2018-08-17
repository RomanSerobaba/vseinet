<?php

namespace AppBundle\Bus\User\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Enum\ContactTypeCode;

class Contact
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $typeCode;

    /**
     * @Assert\Type(type="string")
     */
    public $value;

    /**
     * @Assert\Type(type="string")
     */
    public $comment;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isMain;

    /**
     * @Assert\Type(type="string")
     */
    public $typeCodeTitle;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isPhone;
    

    public function __construct($id, $typeCode, $value, $comment, $isMain)
    {
        $this->id = $id;
        $this->typeCode = $typeCode;
        $this->value = $value;
        $this->comment = $comment;
        $this->isMain = $isMain;
        $this->typeCodeTitle = ContactTypeCode::getTitle($typeCode);
        $this->isPhone = ContactTypeCode::MOBILE === $typeCode || ContactTypeCode::PHONE === $typeCode;
    }
}
