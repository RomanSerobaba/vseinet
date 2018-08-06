<?php 

namespace ContentBundle\Bus\Statistics\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetFulfillmentStatsQuery extends Message 
{
    /**
     * @Assert\Type(type="datetime")
     */
    public $fromDate;

    /**
     * @Assert\Type(type="datetime")
     */
    public $toDate;

    /**
     * @Assert\Type(type="integer")
     */
    public $managerId;

    /**
     * @Assert\Choice({
     *     "",
     *     "in-process", 
     *     "skipped", 
     *     "done"
     * }, strict=true)
     * @VIA\Description("
     *     '' => все,
     *     in-process => в процессе,
     *     skipped => пропущено,
     *     done => готово
     * ")
     */
    public $status;

    /**
     * @Assert\Choice({
     *     "",
     *     "hasnt-images", 
     *     "hasnt-brand", 
     *     "hasnt-color", 
     *     "hasnt-model", 
     *     "hasnt-description", 
     *     "hand-manual-link", 
     *     "hasnt-manufacturer-link"
     * }, strict=true)
     * @VIA\Description("
     *     '' => -,
     *     hasnt-images => нет изображений, 
     *     hasnt-brand => не указан производитель, 
     *     hasnt-color => нет цвета, 
     *     hasnt-model => не указана модель, 
     *     hasnt-description => нет описания, 
     *     hasnt-manual-link => нет ссылки на инструкцию, 
     *     hasnt-manufacturer-link => нет ссылки на сайт производителя  
     * ")
     */
    public $fillType;

    /**
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(1)
     */
    public $page;

    /**
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(25)
     */
    public $limit;
}