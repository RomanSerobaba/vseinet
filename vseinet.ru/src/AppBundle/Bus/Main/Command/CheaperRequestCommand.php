<?php 

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

class CheaperRequestCommand extends Message
{
    /**
     * @Assert\Type(type="AppBundle\Entity\BaseProduct")
     */
    public $product;

    /**
     * @Assert\NotBlank(message="Выберите населенный пункт")
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type(type="AppBundle\Bus\User\Query\DTO\UserData")
     * @Assert\Valid
     */
    public $userData;

    /**
     * @Assert\NotBlank(message="Укажите цену товара у конкурента")
     * @Assert\Type(type="numeric")
     */
    public $competitorPrice;

    /**
     * @Assert\NotBlank(message="Укажите сслыку на карточку товара на сайте конкурента")
     * @Assert\Type(type="string")
     */
    public $competitorLink;

    /**
     * @Assert\Type(type="string")
     */
    public $comment;

    /**
     * @Assert\All({
     *     @Assert\Type(type="AppBundle\Entity\Competitor")
     * })
     */
    public $competitors;
}
