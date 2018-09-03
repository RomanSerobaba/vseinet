<?php 

namespace AppBundle\Enum;

use AppBundle\Annotation as VIA;

class BaseProductImage
{
    const SIZE_XS = 'xs';
    const SIZE_SM = 'sm';
    const SIZE_MD = 'md';
    const SIZE_LG = 'lg';
    const SIZE_XL = 'xl';

    public static function getSize(string $size): string
    {
        switch ($size) {
            case self::SIZE_XS:
                return 60;

            case self::SIZE_SM:
                return 100;

            case self::SIZE_MD:
                return 200;

            case self::SIZE_LG:
                return 280;

            case self::SIZE_XL:
                return 800;
        }

        throw new \LogicException("Wrong image size");
    }

    public static function buildSrc(string $webpath, string $basename, string $size): string
    {
        return $webpath.DIRECTORY_SEPARATOR.$basename.'_'.self::getSize($size).'.jpg';
    }
}
