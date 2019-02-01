<?php

namespace AdminBundle\Bus\Competitor\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class AddRevisionCommand extends Message
{
    /**
     * @Assert\Type(type="numeric")
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Выберите конкурента")
     * @Assert\Type(type="integer")
     */
    public $competitorId;

    /**
     * @Assert\NotBlank(message="Выберите товар")
     * @Assert\Type(type="numeric")
     */
    public $baseProductId;

    /**
     * @Assert\Type(type="string")
     */
    public $link;

    /**
     * @Assert\Type(type="integer")
     */
    public $competitorPrice;
}
