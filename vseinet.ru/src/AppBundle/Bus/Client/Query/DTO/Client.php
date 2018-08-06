<?php 

namespace AppBundle\Bus\Client\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Client 
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     */
    public $redirectUri;


    public function __construct($id, $name, $redirectUris)
    {
        $this->id = $id;
        $this->name = $name;
        $this->redirectUri = $redirectUris[0];
    }
}