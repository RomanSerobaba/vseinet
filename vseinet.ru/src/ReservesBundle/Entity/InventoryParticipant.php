<?php

namespace ReservesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * InventoryParticipant
 *
 * @ORM\Table(name="inventory_participant")
 * @ORM\Entity(repositoryClass="ReservesBundle\Repository\InventoryParticipantRepository")
 */
class InventoryParticipant
{
    
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    /**
     * @var integer
     * @ORM\Column(name="inventory_did", type="integer")
     * @ORM\Id
     */
    private $inventoryDId;

    /**
     * @var integer
     * @ORM\Column(name="participant_id", type="integer")
     * @ORM\Id
     */
    private $participantId;
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    /**
     * Получить уникальный идентификатор документа инвентаризации
     *
     * @return integer
     */
    public function getInventoryDId()
    {
        return $this->inventoryDId;
    }

    /**
     * Установить уникальный идентификатор документа инвентаризации
     *
     * @param integer $inventoryDId
     *
     * @return InventoryParticipant
     */
    public function setInventoryDId($inventoryDId)
    {
        $this->inventoryDId = $inventoryDId;

        return $this;
    }

    /**
     * Set participantId
     *
     * @param integer $participantId
     *
     * @return InventoryParticipant
     */
    public function setParticipantId($participantId)
    {
        $this->participantId = $participantId;

        return $this;
    }

    /**
     * Get participantId
     *
     * @return integer
     */
    public function getParticipantId()
    {
        return $this->participantId;
    }

    // </editor-fold>
}
