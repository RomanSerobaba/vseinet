<?php

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class CheaperRequestCommand extends Message
{
    /**
     * @Assert\Type("integer")
     */
    public $baseProductId;

    /**
     * @Assert\NotBlank(message="Выберите населенный пункт")
     * @Assert\Type("integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type("AppBundle\Bus\User\Query\DTO\UserData")
     * @Assert\Valid
     */
    public $userData;

    /**
     * @Assert\NotBlank(message="Укажите цену товара у конкурента")
     * @Assert\Type("integer")
     */
    public $competitorPrice;

    /**
     * @Assert\NotBlank(message="Укажите сслыку на карточку товара на сайте конкурента")
     * @Assert\Type("string")
     */
    public $competitorLink;

    /**
     * @Assert\Type("string")
     */
    public $comment;
}
