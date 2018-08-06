<?php 

namespace OrgBundle\Bus\Complaint\Query\DTO;

use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ComplaintComment
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $complaintId;

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
     * ComplaintComment constructor.
     *
     * @param $id
     * @param $complaintId
     * @param $text
     * @param $fullname
     * @param $createdAt
     * @param $createdBy
     */
    public function __construct($id, $complaintId, $text, $fullname, $createdAt, $createdBy)
    {
        $this->id = $id;
        $this->complaintId = $complaintId;
        $this->text = $text;
        $this->fullname = $fullname;
        $this->createdAt = $createdAt;
        $this->createdBy = $createdBy;
    }
}