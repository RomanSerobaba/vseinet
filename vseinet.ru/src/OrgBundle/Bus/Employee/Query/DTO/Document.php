<?php

namespace OrgBundle\Bus\Employee\Query\DTO;

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
     * Название документа
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * Признак обязательности
     * @Assert\Type(type="boolean")
     */
    public $isNecessary;

    /**
     * Признак возможности комментария
     * @Assert\Type(type="boolean")
     */
    public $isCommentAllowed;

    /**
     * Признак возможности установки срока действия
     * @Assert\Type(type="boolean")
     */
    public $hasDueDate;

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


    /**
     * Document constructor.
     * @param $documerntId
     * @param $name
     * @param $isNecessary
     * @param $isCommentAllowed
     * @param $hasDueDate
     * @param $userDocumerntId
     * @param $comment
     * @param $isChecked
     * @param null $dueDate
     */
    public function __construct(
        $documerntId,
        $name,
        $isNecessary,
        $isCommentAllowed,
        $hasDueDate,
        $userDocumerntId=null,
        $comment=null,
        $isChecked=null,
        $dueDate=null
    )
    {
        $this->documerntId = $documerntId;
        $this->name = $name;
        $this->isNecessary = $isNecessary;
        $this->isCommentAllowed = $isCommentAllowed;
        $this->hasDueDate = $hasDueDate;
        $this->userDocumerntId = $userDocumerntId;
        $this->comment = $comment;
        $this->isChecked = $isChecked;
        $this->dueDate = $dueDate;
    }
}