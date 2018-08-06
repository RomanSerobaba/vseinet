<?php

namespace OrgBundle\Bus\Contact\Query;

use AppBundle\Annotation as VIA;
use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetOfficeNumbersQuery extends Message
{
    /**
     * @Assert\Choice({"mobile", "phone", "email", "skype", "icq", "custom"})
     */
    public $contactType;

    /**
     * @VIA\Description("Select by Department id")
     * @Assert\Type(type="numeric")
     */
    public $departmentId;

    /**
     * @VIA\Description("Select by Employee id")
     * @Assert\Type(type="numeric")
     */
    public $employeeId;

    /**
     * @VIA\Description("Show also not used numbers")
     * @Assert\Type(type="boolean")
     */
    public $withFree;
}