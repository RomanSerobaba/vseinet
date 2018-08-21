<?php 

namespace AppBundle\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use AppBundle\Doctrine\DBAL\ValueObject\Point;

class PointType extends Type
{
    const POINT = 'point';

    public function getName()
    {
        return self::POINT;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'POINT';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }
        
        list($longitude, $latitude) = explode(',', $value);

        return new Point($latitude, $longitude);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Point) {
            $value = $value->getLongitude().','.$value->getLatitude();
        }

        return $value;
    }

    public function canRequireSQLConversion()
    {
        return true;
    }
}
