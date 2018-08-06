<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser;

/**
 * Описание вкладок
 * public function getSheets() {
 *     return [
 *         'sheet index or name' => [
 *             // required
 *             'fields' => [
 *                 'field name' => 'title',
 *             ],
 *             // optional, default 1
 *             'startRow' => 1,
 *         ],     
 *     ];
 * }
 */
abstract class AbstractStrategy
{
    /**
     * Read only data
     * 
     * @var bool 
     */
    protected $readDataOnly = false;

    /**
     * Leave whitespace
     * 
     * @var bool 
     */
    protected $leaveWhitespace = false;

    /**
     * Pricelist name
     * 
     * @var string
     */
    protected $pricelistName;

    /**
     * Keep categories
     * 
     * @var bool
     */
    protected $isKeepCategories = false;


    /**
     * @param string $pricelistName
     */
    public function __construct($pricelistName)
    {
        $this->pricelistName = $pricelistName;
    }

    /**
     * @return bool
     */
    public function getReadDataOnly()
    {
        return $this->readDataOnly; 
    }

    /**
     * @return bool
     */
    public function getLeaveWhitespace()
    {
        return $this->leaveWhitespace;
    }

    /**
     * @return bool
     */
    public function getIsKeepCategories()
    {
        return $this->isKeepCategories;
    }
}