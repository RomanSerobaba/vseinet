<?php 

namespace AppBundle\Bus\User\Query\DTO;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class Account 
{
    /**
     * @Assert\Type(type="AppBunlde\Bus\User\Query\DTO\UserInfo")
     */
    public $info;

    /**
     * @Assert\Type(type="array<AppBunlde\Bus\User\Query\DTO\Contact>")
     */
    public $contacts;

    /**
     * @Assert\Type(type="array<AppBunlde\Bus\User\Query\DTO\Address>")
     */
    public $addresses;


    public function __construct($info, $contacts, $addresses)
    {
        $this->info = $info;
        $this->contacts = $contacts;
        $this->addresses = $addresses;
    }
}
