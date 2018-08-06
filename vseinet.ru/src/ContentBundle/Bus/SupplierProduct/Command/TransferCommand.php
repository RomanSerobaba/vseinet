<?php 

namespace ContentBundle\Bus\SupplierProduct\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class TransferCommand extends Message
{
    /**
     * @Assert\NotBlank(message="Значение ids не должно быть пустым")
     * @Assert\All({
     *     @Assert\Type(type="integer")
     * })
     * @VIA\Description("Товары поставщика")
     */
    public $ids;
    
    /**
     * @Assert\NotBlank(message="Значение categoryId не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Категория сайта")
     */
    public $categoryId;
}