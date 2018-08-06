<?php

namespace ContentBundle\Bus\Parser\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class GeDetailValuesQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение detailId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $detailId;
}