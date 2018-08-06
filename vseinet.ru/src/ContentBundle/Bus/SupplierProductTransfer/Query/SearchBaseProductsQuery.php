<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class SearchBaseProductsQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение q не должно быть пустым")
     * @Assert\Type(type="string")
     * @VIA\Description("Поисковый запрос")
     */
    public $q;

    /**
     * @VIA\Description("Количество результатов")
     * @Assert\Type(type="integer")
     * @Assert\Range(
     *     min=1,
     *     minMessage="Количество результатов не должно быть меньше {{ limit }}",
     *     max=100,
     *     maxMessage="Количество результатов не должно быть более {{ limit }}"
     * )
     * @VIA\DefaultValue(25)
     */
    public $limit; 
}