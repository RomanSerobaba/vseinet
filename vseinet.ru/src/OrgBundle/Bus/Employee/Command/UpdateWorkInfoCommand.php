<?php

namespace OrgBundle\Bus\Employee\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateWorkInfoCommand extends Message
{
    /**
     * @VIA\Description("Employee id")
     * @Assert\Type(type="integer")
     * @Assert\NotBlank(
     *     message="Employee id should not be blank."
     * )
     */
    public $id;

    /**
     * @VIA\Description("Employees roles")
     * @Assert\Type(type="array")
     * @Assert\All({
     *     @Assert\Type(type="numeric")
     * })
     */
    public $subrolesIds;

    /**
     * @VIA\Description("Employees geo room")
     * @Assert\Type(type="numeric")
     */
    public $geoRoomId;

    /**
     * @VIA\Description("Employees contact phone")
     * @Assert\Type(type="numeric")
     */
    public $contactId;

    /**
     * @VIA\Description("Employees cashDesks")
     * @Assert\Type(type="array")
     * @Assert\All({
     *     @Assert\Type(type="numeric")
     * })
     */
    public $cashDeskIds;
}