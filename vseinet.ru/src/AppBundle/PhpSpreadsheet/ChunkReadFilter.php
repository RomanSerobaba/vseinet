<?php 

namespace AppBundle\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ChunkReadFilter implements IReadFilter
{
    protected $startRow;

    protected $endRow;

    /**
     * @param int $startRow  
     * @param int $chunkSize 
     */
    public function __construct(int $startRow, int $chunkSize)
    {
        $this->startRow = $startRow;
        $this->endRow = $startRow + $chunkSize;
    }

    /**
     * @inheritdoc
     */
    public function readCell($column, $row, $worksheetName = '')
    {
        return 1 == $row || ($row >= $this->startRow && $row < $this->endRow);
    }
}