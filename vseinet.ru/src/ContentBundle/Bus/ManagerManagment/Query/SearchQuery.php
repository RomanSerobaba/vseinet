<?php 

namespace ContentBundle\Bus\ManagerManagment\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class SearchQuery extends Message
{
    /**
     * @VIA\Description("Поисковый запрос")
     * @Assert\NotBlank(message="Значение q не должно быть пустым")
     * @Assert\Type(type="string")
     */
    public $q;

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