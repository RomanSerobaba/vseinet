<?php 

namespace AppBundle\Enum;

class BaseProductImageSize
{
    const XS = 'xs';
    const SM = 'sm';
    const MD = 'md';
    const LG = 'lg';
    const XL = 'xl';

    public static function getSize($size)
    {
        switch ($size) {
            case self::XS:
                return 60;

            case self::SM:
                return 100;

            case self::MD:
                return 200;

            case self::LG:
                return 280;

            case self::XL:
                return 800;
        }

        throw new \LogicException("Wrong image size");
    }

    public static function buildSrc($webpath, $basename, $size)
    {
        return $webpath.DIRECTORY_SEPARATOR.$basename.'_'.self::getSize($size).'.jpg';
    }
}
