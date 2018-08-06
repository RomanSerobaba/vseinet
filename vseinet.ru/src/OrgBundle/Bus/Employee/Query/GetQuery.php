<?php 

namespace OrgBundle\Bus\Employee\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetQuery extends Message 
{
    
    /**
     * @VIA\Description("Показывать уволенных")
     * @Assert\Type(type="boolean")
     */
    public $withFired;
    
    /**
     * @VIA\Description("Маска поиска сотрудника")
     * @Assert\Type(type="string")
     */
    public $q;
    
}