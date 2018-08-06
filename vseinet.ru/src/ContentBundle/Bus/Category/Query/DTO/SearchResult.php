<?php 

namespace ContentBundle\Bus\Category\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class SearchResult
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $path;
    

    public function __construct($id, $path)
    {
        $this->id = $id;
        $this->path = $path;
    }
}