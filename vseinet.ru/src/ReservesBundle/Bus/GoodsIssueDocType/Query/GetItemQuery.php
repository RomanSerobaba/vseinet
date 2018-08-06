<?php 

namespace ReservesBundle\Bus\GoodsIssueDocType\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetItemQuery extends Message 
{
    /**
     * @VIA\Description("Идентификатор строки (позиции)")
     * @Assert\NotBlank(message="Идентификатор строки (позиции) документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;
}