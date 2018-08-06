<?php 

namespace ContentBundle\Bus\Brand\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SearchQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение name не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @VIA\Description("Количество результатов")
     * @Assert\Type(type="integer")
     * @Assert\Range(
     *     min=1,
     *     minMessage="Количество результатов не должно быть меньше {{ limit }}",
     *     max=25,
     *     maxMessage="Количество результатов не должно быть более {{ limit }}"
     * )
     * @VIA\DefaultValue(10)
     */
    public $limit;
}