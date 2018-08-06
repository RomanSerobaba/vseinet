<?php

namespace DocumentBundle\Bus\Comment\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class Comment
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор комментария")
     */
    private $id;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор претензии")
     */
    private $documentId;

    /**
     * @Assert\DateTime
     * @VIA\Description("Дата создания документа")
     */
    private $createdAt;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор автора")
     */
    private $createdBy;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование автора")
     */
    private $createdName;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Человекочитаемый заголовок документа")
     */
    private $comment;

    public function __construct(
            $id, $documentId,
            $createdAt, $createdBy, $createdName,
            $comment)
    {
        $this->id = $id;
        $this->documentId = $documentId;

        $this->createdAt = $createdAt;
        $this->createdBy = $createdBy;
        $this->createdName = $createdName;

        $this->comment = $comment;
    }

}
