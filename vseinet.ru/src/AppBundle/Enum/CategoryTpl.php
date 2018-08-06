<?php 

namespace AppBundle\Enum;

use AppBundle\Annotation as VIA;

class CategoryTpl
{
    /**
     * @VIA\Description("без шаблона")
     */
    const NONE = 'none';

    /**
     * @VIA\Description("ручной шаблон")
     */
    const MANUAL = 'manual';

    /**
     * @VIA\Description("автоматический шаблон")
     */
    const AUTO = 'auto';
}