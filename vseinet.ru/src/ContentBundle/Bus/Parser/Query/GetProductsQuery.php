<?php

namespace ContentBundle\Bus\Parser\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @deprecated
 */
class GetProductsQuery extends Message 
{
    /**
     * @Assert\Type(type="integer")
     */
    public $sourceId;

    /**
     * @Assert\NotBlank(message="Значение categoryId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\Type(type="date")
     */
    public $fromDate;

    /**
     * @Assert\Type(type="date")
     */
    public $toDate;

    /**
     * @Assert\Choice({"all", "success", "failure", "notfound", "unknown"}, strict=true)
     * @VIA\DefaultValue("all")
     */
    public $status;
}