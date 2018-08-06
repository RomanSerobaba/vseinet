<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Города
 *
 * @ORM\Table(name="geo_city")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\GeoCityRepository")
 */

class GeoCity
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
     * @ORM\Column(name="geo_region_id", type="integer")
     */
    private $geoRegionId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="is_central", type="boolean")
     */
    private $isCentral;
    
    /**
     * @var string
     *
     * @ORM\Column(name="phone_code", type="string")
     */
    private $phoneCode;
    
    /**
     * @var string
     *
     * @ORM\Column(name="""AOGUID""", type="string")
     */
    private $AOGUID;
    
    
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

    // field geoRegionId

    /**
     * Получить идентификатор региона
     *
     * @return int
     */
    public function getGeoRegionId()
    {
        return $this->geoRegionId;
    }

    /**
     * Установить идентификатор региона
     *
     * @param int $geoRegionId
     *
     * @return GeoCity
     */
    public function setGeoRegionId($geoRegionId)
    {
        $this->geoRegionId = $geoRegionId;

        return $this;
    }

    // field name

    /**
     * Получить наименование региона
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Установить наиментвание региона
     *
     * @param string $name
     *
     * @return GeoCity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    // field isCentral

    /**
     * Получить признак центра
     *
     * @return bool
     */
    public function getIsCentral()
    {
        return $this->isCentral;
    }

    /**
     * Установить признак центра
     *
     * @param bool $isCentral
     *
     * @return GeoCity
     */
    public function setIsCentral($isCentral)
    {
        $this->isCentral = $isCentral;

        return $this;
    }

    // field phoneCode

    /**
     * Получить префикс телефонного номера
     *
     * @return string
     */
    public function getPhoneCode()
    {
        return $this->phoneCode;
    }

    /**
     * Установить префикс телефонного номера
     *
     * @param string $phoneCode
     *
     * @return GeoCity
     */
    public function setPhoneCode($phoneCode)
    {
        if (strlen($phoneCode) > 5) throw new BadRequestHttpException('Длинна префикса телефонного номера должна быть не более 5 символов.');
        
        $this->phoneCode = $phoneCode;

        return $this;
    }

    // field AOGUID

    /**
     * Получить префикс телефонного номера
     *
     * @return string
     */
    public function getAOGUID()
    {
        return $this->AOGUID;
    }

    /**
     * Установить префикс телефонного номера
     *
     * @param string $AOGUID
     *
     * @return GeoCity
     */
    public function setAOGUID($AOGUID)
    {
        if (strlen($AOGUID) != 36) throw new BadRequestHttpException('Длинна текстового представления глобального уникального идентификатора должна быть 36 символов.');

        $this->AOGUID = $AOGUID;

        return $this;
    }


    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Переопределение магических методов">
    
    ///////////////////////////
    //
    //  Переопределение магических методов
    //
    
    function __construct()
    {
        $this->isCentral = false;
    }
    
    function __clone()
    {
        $this->id = null;
    }
    
    // </editor-fold>
    
}
