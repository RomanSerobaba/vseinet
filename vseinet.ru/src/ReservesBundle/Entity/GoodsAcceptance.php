<?php
/**
 * @author Denis O. Konashonok
 */

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GoodsAcceptance
 *
 * @ORM\Table(name="goods_acceptance_doc")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\GoodsAcceptanceRepository")
 */
class GoodsAcceptance
{
    use \DocumentBundle\Prototipe\DocumentEntity;

    const STATUS_NEW = 'new';
    const STATUS_COMPLETED = 'completed';
    
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    /**
     * @var \DateTime
     * @ORM\Column(name="arriving_time", type="datetime", nullable=true)
     */
    private $arrivingTime;
    
    /**
     * @var int
     * @ORM\Column(name="geo_room_id", type="integer")
     */
    private $geoRoomId;
    
    /**
     * @var int
     * @ORM\Column(name="geo_room_source", type="integer")
     */
    private $geoRoomSource;
    
    /**
     * @var array
     * @ORM\Column(name="supplyies_documents_ids", type="json")
     */
    private $supplyiesDocumentsIds;
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    /**
     * Get geoRoomId
     * @return int
     */
    public function getGeoRoomId()
    {
        return $this->geoRoomId;
    }

    /**
     * Set geoRoomId
     * @param int $geoRoomId
     * @return GoodsAcceptance
     */
    public function setGeoRoomId($geoRoomId)
    {
        $this->geoRoomId = $geoRoomId;
        return $this;
    }

    /**
     * Get geoRoomSource
     * @return int|null
     */
    public function getGeoRoomSource()
    {
        return $this->geoRoomSource;
    }

    /**
     * Set geoRoomSource
     * @param int|null $geoRoomSource
     * @return GoodsAcceptance
     */
    public function setGeoRoomSource($geoRoomSource = null)
    {
        $this->geoRoomSource = $geoRoomSource;

        return $this;
    }

    /**
     * Get supplyiesDocumentsIds
     * @return array
     */
    public function getSupplyiesDocumentsIds()
    {
        return $this->supplyiesDocumentsIds;
    }

    /**
     * Set supplyiesDocumentsIds
     * @param array|null $supplyiesDocumentsIds
     * @return GoodsAcceptance
     */
    public function setSupplyiesDocumentsIds($supplyiesDocumentsIds = null)
    {
        $this->supplyiesDocumentsIds = $supplyiesDocumentsIds;

        return $this;
    }

    
    /**
     * Получить дату поступления
     * @return \DateTime|null
     */
    public function getArrivingTime()
    {
        return $this->arrivingTime;
    }

    /**
     * Установить дату поступления
     * @param \DateTime|null $arrivingTime
     * @return GoodsAcceptance
     */
    public function setArrivingTime($arrivingTime = null)
    {
        $this->arrivingTime = $arrivingTime;

        return $this;
    }
    
    // </editor-fold>

}
