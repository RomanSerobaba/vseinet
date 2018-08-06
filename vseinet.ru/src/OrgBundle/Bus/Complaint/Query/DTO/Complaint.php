<?php 

namespace OrgBundle\Bus\Complaint\Query\DTO;

use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class Complaint
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $text;

    /**
     * @Assert\Type(type="datetime")
     */
    public $createdAt;

    /**
     * @Assert\Type(type="integer")
     */
    public $createdBy;

    /**
     * @Assert\Type(type="string")
     */
    public $firstname;

    /**
     * @Assert\Type(type="string")
     */
    public $phone;

    /**
     * @Assert\Type(type="string")
     */
    public $email;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isChecked;

    /**
     * @Assert\Type(type="string")
     */
    public $manager;

    /**
     * @Assert\Type(type="string")
     */
    public $managerPhone;

    /**
     * @Assert\Type(type="string")
     */
    public $type;

    /**
     * @Assert\Type(type="array<OrgBundle\Bus\Complaint\Query\DTO\ComplaintComment>")
     */
    public $comments;

    /**
     * Complaint constructor.
     *
     * @param $id
     * @param $text
     * @param $createdAt
     * @param $createdBy
     * @param $firstname
     * @param $phone
     * @param $email
     * @param $isChecked
     * @param $manager
     * @param $managerPhone
     * @param $type
     * @param $comments
     */
    public function __construct(
        $id,
        $text,
        $createdAt,
        $createdBy,
        $firstname,
        $phone,
        $email,
        $isChecked,
        $manager,
        $managerPhone,
        $type,
        $comments=[]
    ) {
        $this->id = $id;
        $this->text = $text;
        $this->createdAt = $createdAt;
        $this->createdBy = $createdBy;
        $this->firstname = $firstname;
        $this->phone = $phone;
        $this->email = $email;
        $this->isChecked = $isChecked;
        $this->manager = $manager;
        $this->managerPhone = $managerPhone;
        $this->type = $type;
        $this->comments = $comments;
    }

    public function setComments($param)
    {
        if (strlen($param) > 2) {
            $array = json_decode($param);
            $this->comments = [];
            foreach ($array as $value) {
                $this->comments[] = new ComplaintComment($value->id, $value->complaintId, $value->text, $value->fullname, $value->createdAt, $value->createdBy);
            }
        }else{
            $this->comments = [];
        }
    }
}