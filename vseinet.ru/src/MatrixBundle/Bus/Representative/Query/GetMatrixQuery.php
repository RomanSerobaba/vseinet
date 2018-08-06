<?php 

namespace MatrixBundle\Bus\Representative\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetMatrixQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор точки")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор шаблона")
     */
    public $templateId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Страница списка товаров шаблона матрицы точки")
     * @VIA\DefaultValue(1)
     */
    public $page;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Количество элементов на странице списка товаров шаблона матрицы точки")
     * @VIA\DefaultValue(50)
     */
    public $limit;
}