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
    public $typeCodeName;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isPhone;
    

    public function __construct($id, $typeCode, $value, $comment = '', $isMain = false)
    {
        $this->id = $id;
        $this->typeCode = $typeCode;
        $this->value = $value;
        $this->comment = $comment;
        $this->isMain = $isMain;
        $this->typeCodeName = ContactTypeCode::getName($typeCode);
        $this->isPhone = ContactTypeCode::MOBILE === $typeCode || ContactTypeCode::PHONE === $typeCode;
    }
}
