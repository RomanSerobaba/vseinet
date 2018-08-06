<?php 

namespace OrgBundle\Bus\Suggestions\Query\DTO;

use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class Suggestion
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
     * @Assert\Type(type="array<OrgBundle\Bus\Suggestions\Query\DTO\SuggestionComment>")
     */
    public $comments;


    public function setComments($param)
    {
        if (strlen($param) > 2) {
            $array = json_decode($param);
            $this->comments = [];
            foreach ($array as $value) {
                $this->comments[] = new SuggestionComment($value->id, $value->clientSuggestionId, $value->text, $value->fullname, $value->createdAt, $value->createdBy);
            }
        }else{
            $this->comments = [];
        }

//        $this->comments = array_map(function($obj) {
//            return new \OrgBundle\Bus\Complaint\Query\DTO\ComplaintComment($obj);
//        }, json_decode($comments, true));
    }


}