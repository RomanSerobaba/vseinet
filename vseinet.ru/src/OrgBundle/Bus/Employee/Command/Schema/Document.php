<?php

namespace OrgBundle\Bus\Employee\Command\Schema;

use Symfony\Component\Validator\Constraints as Assert;

class Document
{
    /**
     * Идентификатор документа
     * @Assert\Type(type="numeric", message="DocumentId is not number")
     * @Assert\NotBlank()
     */
    public $documerntId;

    /**
     * Идентификатор привязки документа к пользователю
     * @Assert\Type(type="numeric")
     */
    public $userDocumerntId;

    /**
     * @Assert\Type(type="string")
     */
    public $comment;

    /**
     * Признак проверки документа
     * @Assert\Type(type="boolean")
     */
    public $isChecked;

    /**
     * Дата окончания срока действия документа
     * @Assert\Type(type="DateTime")
     */
    public $dueDate;
}