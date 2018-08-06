<?php 

namespace AppBundle\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\IOFactory;

class Reader
{
    /**
     * @param string $filename     
     * @param integer $startRow     
     * @param integer $chunkSize    
     * @param bool $readDataOnly
     *  
     * @return PhpOffice/PhpSpreadsheet/Reader/IReader              
     */
    public function load($filename, int $startRow = null, int $chunkSize = null, bool $readDataOnly = false)
    {
        $reader = IOFactory::createReaderForFile($filename);
        if (null !== $startRow && null !== $chunkSize) {
            $reader->setReadFilter(new ChunkReadFilter($startRow, $chunkSize));
        }
        $reader->setReadDataOnly($readDataOnly);

        return $reader->load($filename);
    }
}