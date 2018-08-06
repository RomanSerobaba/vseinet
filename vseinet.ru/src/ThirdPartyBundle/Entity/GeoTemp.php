<?php

namespace ThirdPartyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoTemp
 *
 * @ORM\Table(name="geo_temp")
 * @ORM\Entity(repositoryClass="ThirdPartyBundle\Repository\GeoTempRepository")
 */
class GeoTemp
{
    const IS_PROCESSED_YES = 1;
    const IS_PROCESSED_NO = 0;

    const ACTSTATUS_1 = 1;
    const ACTSTATUS_0 = 0;

    const CENTSTATUS_0 = 0; // не центр

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="`AOGUID`", type="string", length=36)
     */
    protected $AOGUID;

    /**
     * @var string
     *
     * @ORM\Column(name="`PARENTGUID`", type="string", length=36)
     */
    protected $PARENTGUID;

    /**
     * @var string
     *
     * @ORM\Column(name="`OFFNAME`", type="string", length=120)
     */
    protected $OFFNAME;

    /**
     * @var string
     *
     * @ORM\Column(name="`SHORTNAME`", type="string", length=10)
     */
    protected $SHORTNAME;

    /**
     * @var int
     *
     * @ORM\Column(name="`AOLEVEL`", type="integer")
     */
    protected $AOLEVEL;

    /**
     * @var int
     *
     * @ORM\Column(name="`ACTSTATUS`", type="integer")
     */
    protected $ACTSTATUS;

    /**
     * @var int
     *
     * @ORM\Column(name="`CENTSTATUS`", type="integer")
     */
    protected $CENTSTATUS;

    /**
     * @var int
     *
     * @ORM\Column(name="is_processed", type="integer")
     */
    protected $is_processed;

    /**
     * Set aOGUID
     *
     * @param string $AOGUID
     *
     * @return GeoTemp
     */
    public function setAOGUID($AOGUID)
    {
        $this->AOGUID = $AOGUID;

        return $this;
    }

    /**
     * Get aOGUID
     *
     * @return string
     */
    public function getAOGUID()
    {
        return $this->AOGUID;
    }

    /**
     * Set pARENTGUID
     *
     * @param string $PARENTGUID
     *
     * @return GeoTemp
     */
    public function setPARENTGUID($PARENTGUID)
    {
        $this->PARENTGUID = $PARENTGUID;

        return $this;
    }

    /**
     * Get pARENTGUID
     *
     * @return string
     */
    public function getPARENTGUID()
    {
        return $this->PARENTGUID;
    }

    /**
     * Set oFFNAME
     *
     * @param string $OFFNAME
     *
     * @return GeoTemp
     */
    public function setOFFNAME($OFFNAME)
    {
        $this->OFFNAME = $OFFNAME;

        return $this;
    }

    /**
     * Get oFFNAME
     *
     * @return string
     */
    public function getOFFNAME()
    {
        return $this->OFFNAME;
    }

    /**
     * Set sHORTNAME
     *
     * @param string $SHORTNAME
     *
     * @return GeoTemp
     */
    public function setSHORTNAME($SHORTNAME)
    {
        $this->SHORTNAME = $SHORTNAME;

        return $this;
    }

    /**
     * Get sHORTNAME
     *
     * @return string
     */
    public function getSHORTNAME()
    {
        return $this->SHORTNAME;
    }

    /**
     * Set aOLEVEL
     *
     * @param integer $AOLEVEL
     *
     * @return GeoTemp
     */
    public function setAOLEVEL($AOLEVEL)
    {
        $this->AOLEVEL = $AOLEVEL;

        return $this;
    }

    /**
     * Get aOLEVEL
     *
     * @return int
     */
    public function getAOLEVEL()
    {
        return $this->AOLEVEL;
    }

    /**
     * Set aCTSTATUS
     *
     * @param integer $ACTSTATUS
     *
     * @return GeoTemp
     */
    public function setACTSTATUS($ACTSTATUS)
    {
        $this->ACTSTATUS = $ACTSTATUS;

        return $this;
    }

    /**
     * Get aCTSTATUS
     *
     * @return int
     */
    public function getACTSTATUS()
    {
        return $this->ACTSTATUS;
    }

    /**
     * Set cENTSTATUS
     *
     * @param integer $CENTSTATUS
     *
     * @return GeoTemp
     */
    public function setCENTSTATUS($CENTSTATUS)
    {
        $this->CENTSTATUS = $CENTSTATUS;

        return $this;
    }

    /**
     * Get cENTSTATUS
     *
     * @return int
     */
    public function getCENTSTATUS()
    {
        return $this->CENTSTATUS;
    }

    /**
     * Set is_processed
     *
     * @param int $isProcessed
     *
     * @return GeoTemp
     */
    public function setIsProcessed($isProcessed)
    {
        $this->is_processed = $isProcessed;

        return $this;
    }

    /**
     * Get is_processed
     *
     * @return int
     */
    public function getIsProcessed()
    {
        return $this->is_processed;
    }

    /**
     * @param array $row
     */
    public function fillRow(array $row): void
    {
        foreach ($row as $key => $value) {
            $methodName = 'set'.$key;

            if ( !method_exists($this, $methodName) ) {
                continue;
            }

            if (empty($value)) {
                $value = '';
            }

            $this->$methodName($value);
        }

        $this->setIsProcessed(self::IS_PROCESSED_NO);
    }
}