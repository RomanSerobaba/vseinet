<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Помещения
 *
 * @ORM\Table(name="geo_room")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\GeoRoomRepository")
 */

class GeoRoom
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
     * @ORM\Column(name="geo_point_id", type="integer")
     */
    private $geoPointId;

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
     * @var bool
     *
     * @ORM\Column(name="is_default", type="boolean")
     */
    private $isDefault;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="has_auto_release", type="boolean")
     */
    private $hasAutoRelease;
    
    /**
     * @var int
     *
     * @ORM\Column(name="write_off_order", type="integer")
     */
    private $writeOfOrder;
    
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
     * Получить идентификатор точки
     *
     * @return int
     */
    public function getGeoPointId()
    {
        return $this->geoPointId;
    }

    /**
     * Установить идентификатор точки
     *
     * @param int $geoPointId
     *
     * @return GeoRoom
     */
    public function setGeoPointId($geoPointId)
    {
        $this->geoPointId = $geoPointId;

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
     * @return GeoRoom
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    // field isDefault

    /**
     * Получить признак исключительного приоритета
     *
     * @return bool
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * Установить признак исключительного приоритета
     *
     * @param bool $isDefault
     *
     * @return GeoRoom
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;

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
     * @return GeoRoom
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
     * @return GeoRoom
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }


    // field hasAutoRelease

    /**
     * Получить что-то
     *
     * @return bool
     */
    public function getHasAutoRelease()
    {
        return $this->hasAutoRelease;
    }

    /**
     * Установить что-то
     *
     * @param bool $hasAutoRelease
     *
     * @return GeoRoom
     */
    public function setHasAutoRelease($hasAutoRelease)
    {
        $this->hasAutoRelease = $hasAutoRelease;

        return $this;
    }

    // field writeOfOrder

    /**
     * Получить что-то
     *
     * @return int
     */
    public function getWriteOfOrder()
    {
        return $this->writeOfOrder;
    }

    /**
     * Установить что-то
     *
     * @param int $writeOfOrder
     *
     * @return GeoRoom
     */
    public function setWriteOfOrder($writeOfOrder)
    {
        $this->writeOfOrder = $writeOfOrder;

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
