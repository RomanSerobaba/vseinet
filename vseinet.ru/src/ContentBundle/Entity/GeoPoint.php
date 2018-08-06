<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Строения
 *
 * @ORM\Table(name="geo_point")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\GeoPointRepository")
 */

class GeoPoint
{
    
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    ///////////////////////////
    //
    //  Поля
    //
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_city_id", type="integer")
     */
    private $geoCityId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string")
     */
    private $code;
    
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     */
    private $type;
    
    /**
     * @var int
     *
     * @ORM\Column(name="geo_address_id", type="integer")
     */
    private $geoAddressId;
    
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    ///////////////////////////
    //
    //  Методы
    //
    
    // field id 

    /**
     * Получить идентификатор
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    // field geoPointId

    /**
     * Получить идентификатор города
     *
     * @return int
     */
    public function getGeoCityId()
    {
        return $this->geoCityId;
    }

    /**
     * Установить идентификатор города
     *
     * @param int $geoCityId
     *
     * @return GeoPoint
     */
    public function setGeoCityId($geoCityId)
    {
        $this->geoCityId = $geoCityId;

        return $this;
    }

    // field name

    /**
     * Получить наименование точки
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Установить наиментвание точки
     *
     * @param string $name
     *
     * @return GeoPoint
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    // field code

    /**
     * Получить код
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Установить код
     *
     * @param string $code
     *
     * @return GeoPoint
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    // field type

    /**
     * Получить тип
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Установить тип
     *
     * @param string $type
     *
     * @return GeoPoint
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }


    // field geoAddressId

    /**
     * Получить что-то
     *
     * @return int
     */
    public function getGeoAddressId()
    {
        return $this->geoAddressId;
    }

    /**
     * Установить что-то
     *
     * @param int $geoAddresId
     *
     * @return GeoPoint
     */
    public function setGeoAddressId($geoAddresId)
    {
        $this->geoAddressId = $geoAddresId;

        return $this;
    }

    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Переопределение магических методов">
    
    ///////////////////////////
    //
    //  Переопределение магических методов
    //
    
    function __clone()
    {
        $this->id = null;
    }
    
    // </editor-fold>
    
}
