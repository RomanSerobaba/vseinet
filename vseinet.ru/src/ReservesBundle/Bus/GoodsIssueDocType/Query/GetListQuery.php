<?php 
namespace ReservesBundle\Bus\GoodsIssueDocType\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetListQuery extends Message 
{
    /**
     * @VIA\Description("Показать только типы претензий доступные для интерактивного создания")
     * @Assert\Type(type="boolean")
     */
    public $onlyInteractive;

    /**
     * @VIA\Description("Показать с неактивными типами претензий")
     * @Assert\Type(type="boolean")
     */
    public $withInActive;

}