<?php 

namespace ContentBundle\Bus\Detail\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetByIdsQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение ids не дожно быть пустым")
     * @Assert\All({
     *     @Assert\Type(type="integer")
     * })
     */
    public $ids;
}