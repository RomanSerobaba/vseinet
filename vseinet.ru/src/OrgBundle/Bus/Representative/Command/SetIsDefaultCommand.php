<?php 

namespace OrgBundle\Bus\Representative\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SetIsDefaultCommand extends Message
{
    /**
     * @VIA\Description("Representative id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Geo room id")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;

    /**
     * @VIA\Description("ТПункт зачисления под реализацию")
     * @Assert\Type(type="boolean")
     */
    public $isDefault;
}