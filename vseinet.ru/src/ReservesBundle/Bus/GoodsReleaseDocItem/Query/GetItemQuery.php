<?php 

namespace ReservesBundle\Bus\GoodsReleaseDocItem\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetItemQuery extends Message 
{
    /**
     * @VIA\Description("Идентификатор документа комплектации/разкомплектации")
     * @Assert\NotBlank(message="Идентификатор документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $goodsReleaseId;

    /**
     * @VIA\Description("Идентификатор строки (позиции)")
     * @Assert\NotBlank(message="Идентификатор строки (позиции) документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;
}