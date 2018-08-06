<?php 
namespace ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetDeltaResultQuery extends Message 
{
    /**
     * @VIA\Description("UUID ранее выполненной команды.")
     * @Assert\Uuid
     * @Assert\NotBlank(message="UUID ранее выполненной команды обязательно должен быть указан.")
     */
    public $uuid;
}