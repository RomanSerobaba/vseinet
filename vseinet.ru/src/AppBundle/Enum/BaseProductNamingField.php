<?php 

namespace AppBundle\Enum;

use AppBundle\Annotation as VIA;

class BaseProductNamingField
{
    /**
     * @VIA\Description("базовое наименование")
     */
    const BASENAME = 'basename'; 

    /**
     * @VIA\Description("бренд")
     */
    const BRAND = 'brand'; 

    /**
     * @VIA\Description("модель")
     */
    const MODEL = 'model';

    /**
     * @VIA\Description("составной цвет")
     */
    const COLOR_COMPOSITE = 'color_composite';
}