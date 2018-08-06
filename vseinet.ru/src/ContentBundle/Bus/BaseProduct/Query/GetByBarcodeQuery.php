<?php 

namespace ContentBundle\Bus\BaseProduct\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GetByBarcodeQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение штрихкода не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $barCode;
}