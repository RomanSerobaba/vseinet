<?php

namespace AppBundle\Doctrine\ORM\Query;

use Doctrine\ORM\Query\ResultSetMapping;

class DTORSM extends ResultSetMapping
{
    const ARRAY_INDEX = 'array index';
    const ARRAY_ASSOC = 'array assoc';
    const OBJECT_SINGLE = 'object single';

    /**
     * @var string
     */
    protected $DTO;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @param string $DTO
     * @param string $mode
     */
    public function __construct($DTO, $mode = self::ARRAY_INDEX)
    {
        $this->setDTO($DTO);
        $this->setMode($mode);
    }

    /**
     * Set DTO.
     * 
     * @param string $DTO
     * 
     * @throws \InvalidArgumentException
     * 
     * @return DTORSM
     */
    public function setDTO($DTO)
    {
        if (!class_exists($DTO)) {
            throw new \InvalidArgumentException(sprintf('Class DTO "%s" not found.', $DTO));
        }

        $this->DTO = $DTO;

        return $this;
    }

    /**
     * Get DTO.
     * 
     * @return string
     */
    public function getDTO()
    {
        return $this->DTO;
    }

    /**
     * Set mode.
     * 
     * @param string $mode
     * 
     * @throws \InvalidArgumentException
     * 
     * @return DTORSM
     */
    public function setMode($mode) 
    {
        if (!in_array($mode, [self::ARRAY_INDEX, self::ARRAY_ASSOC, self::OBJECT_SINGLE])) {
            throw new \InvalidArgumentException(sprintf('Invalid mode fetching "%s".', $mode));
        }

        $this->mode = $mode;

        return $this;
    }

    /**
     * Get mode.
     * 
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }
}
