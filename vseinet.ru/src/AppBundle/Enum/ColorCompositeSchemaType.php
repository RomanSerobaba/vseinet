<?php 

namespace AppBundle\Enum;

use AppBundle\Annotation as VIA;

class ColorCompositeSchemaType
{
    /**
     * @VIA\Description("простой цвет")
     */
    const SINGLE = 'single';

    /**
     * @VIA\Description("прозрачный")
     */
    const TRANSPARENT = 'transparent';

    /**
     * @VIA\Description("многоцветный")
     */
    const RAINBOW = 'rainbow';

    /**
     * @VIA\Description("нержавеющая сталь")
     */
    const STEEL = 'steel';

    /**
     * @VIA\Description("цвета в ассортименте")
     */
    const IN_ASSORTMENT = 'in-assortment';

    /**
     * @VIA\Description("металлик")
     */
    const METAL = 'metal';

    /**
     * @VIA\Description("матовый")
     */
    const MATTE = 'matte';

    /**
     * @VIA\Description("перламутровый")
     */
    const PEARL = 'pearl';
}