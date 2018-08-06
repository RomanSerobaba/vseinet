<?php 

namespace OrgBundle\Bus\Suggestions\Query\DTO;

use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SuggestionComment
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $clientSuggestionId;

    /**
     * @Assert\Type(type="string")
     */
    public $text;

    /**
     * @Assert\Type(type="string")
     */
    public $fullname;

    /**
     * @Assert\Type(type="datetime")
     */
    public $createdAt;

    /**
     * @Assert\Type(type="integer")
     */
    public $createdBy;

    /**
     * SuggestionComment constructor.
     *
     * @param $id
     * @param $clientSuggestionId
     * @param $text
     * @param $fullname
     * @param $createdAt
     * @param $createdBy
     */
    public function __construct($id, $clientSuggestionId, $text, $fullname, $createdAt, $createdBy)
    {
        $this->id = $id;
        $this->clientSuggestionId = $clientSuggestionId;
        $this->text = $text;
        $this->fullname = $fullname;
        $this->createdAt = $createdAt;
        $this->createdBy = $createdBy;
    }

}