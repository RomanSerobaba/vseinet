<?php 

namespace AppBundle\Bus\ResourceMethod\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Method
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
     * @Assert\Type(type="integer")
     */
    public $resourceId;

    /**
     * @Assert\Choice({"GET", "POST", "PUT", "PATCH", "DELETE"}, strict=true)
     */
    public $method;

    /**
     * @Assert\Type(type="string")
     */
    public $path;

    /**
     * @Assert\Type(type="array")
     */
    public $parameters;

    /**
     * @Assert\Type(type="array")
     */
    public $responses;

    /**
     * @Assert\Type(type="string")
     */
    public $description;

    /**
     * @Assert\Type(type="integer")
     */
    public $apiMethodId;


    public function __construct($id, $name, $resourceId, $method, $path, $parameters, $responses, $description, $apiMethodId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->resourceId = $resourceId;
        $this->method = $method;
        $this->path = $path;
        $this->parameters = json_decode($parameters, true);
        $this->responses = json_decode($responses, true);
        $this->description = $description;
        $this->apiMethodId = $apiMethodId;
    }
}