<?php 

namespace ContentBundle\Bus\SupplierPricelist\Parser;

use AppBundle\PhpSpreadsheet\Reader;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use RuntimeException;

class Parser
{
    /**
     * Max number column of the title search 
     */
    const HEADERS_MAX_COLUMN = 65;

    /**
     * Max number row of the title search 
     */
    const HEADERS_MAX_ROW = 100;

    /**
     * Number of read rows at a time
     */
    const READ_CHUNK_SIZE = 1000;

    /**
     * Excel reader
     * 
     * @var Reader
     */
    protected $reader;

    /**
     * Data processing strategy
     * 
     * @var StrategyInterface
     */
    protected $strategy;


    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param string $supplierCode 
     * @param string $pricelistName
     * 
     * @return Parser
     * @throws RuntimeException
     */
    public function init($supplierCode, $pricelistName)
    {
        $class = 'ContentBundle\\Bus\\SupplierPricelist\\Parser\\Strategy\\'.$supplierCode.'Strategy';
        if (!class_exists($class)) {
            throw new RuntimeException(sprintf('Class strategy for supplier %s not found', $supplierCode));
        }
        $this->strategy = new $class($pricelistName);

        return $this;
    }

    /**
     * Get startegy
     * 
     * @return StrategyInterface
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * Check titles
     * 
     * @param string $filename
     * 
     * @return bool
     * @throws PhpOffice\PhpSpreadsheet\Exception
     */
    public function check($filename)
    {
        $pricelist = $this->reader->load($filename, 1, self::HEADERS_MAX_ROW, true);
        $sheets = $this->strategy->getSheets();
        foreach ($sheets as $indexOrName => $options) {
            if (is_int($indexOrName)) {
                $sheet = $pricelist->getSheet($indexOrName);
            }
            else {
                $sheet = $pricelist->getSheetByName($indexOrName);
            }
            if (null === $sheet) {
                throw new Exception(sprintf('Вкладка "%s" не найдена', $indexOrName));
            }
            $row = 1;
            $fields = [];
            while (self::HEADERS_MAX_ROW > $row) {
                $found = $this->findFields($sheet, $row, $options['fields']);
                if (!empty($found)) {
                    $fields = $found;
                    if (count($fields) == count($options['fields'])) {
                        break;
                    }
                }
                $row++;
            }

            $lostfields = array_diff_key($options['fields'], $fields);
            if (!empty($lostfields)) {
                $lost = [];
                foreach ($lostfields as $field) {
                    if (is_array($field)) {
                        $lost = array_merge($lost, $field);
                    }
                    else {
                        $lost[] = $field;
                    }
                }

                throw new Exception(sprintf('Не найдены столбцы "%s" во вкладке "%s"', implode('", "', $lost), $indexOrName));
            }
        }

        return true;
    }

    /**
     * Parsing
     * 
     * @param string $filename
     * @param Closure $listener
     */
    public function parse($filename, \Closure $listener)
    {
        $pricelist = $this->reader->load($filename, 1, self::HEADERS_MAX_ROW, true);
        $sheets = $this->strategy->getSheets();
        foreach ($sheets as $indexOrName => $options) {
            if (is_int($indexOrName)) {
                $sheet = $pricelist->getSheet($indexOrName);
            }
            else {
                $sheet = $pricelist->getSheetByName($indexOrName);
            }
            $row = 1;
            while (self::HEADERS_MAX_ROW > $row) {
                $fields = $this->findFields($sheet, $row, $options['fields']);
                if (count($fields) == count($options['fields'])) {
                    break;
                }
                $row++;
            }
            if (!empty($options['startRow'])) {
                $row += $options['startRow'];
            }

            $sheetIndex = $pricelist->getIndex($sheet);

            $pricelist->disconnectWorksheets();
            unset($pricelist);

            while (true) {

                $pricelist = $this->reader->load($filename, $row, self::READ_CHUNK_SIZE, $this->strategy->getReadDataOnly());
                
                $sheet = $pricelist->getSheet($sheetIndex);
                
                $endRow = min($sheet->getHighestRow(), $row + self::READ_CHUNK_SIZE);
                if ($row > $endRow) {
                    break;
                }

                while ($row <= $endRow) {
                    $data = $this->readData($sheet, ++$row, $fields);
                    if (empty($data)) {
                        continue;
                    }
                    if (!is_array($data)) {
                        throw new RuntimeException(sprintf('Должен быть ассоциативный массив или массив ассоциативных массивов, %s', print_r($data, true)));   
                    }
                    if (is_int(key($data))) {
                        foreach ($data as $index => $variant) {
                            if (!is_int($index)) {
                                throw new RuntimeException(sprintf('Должен быть ассоциативный массив или массив ассоциативных массивов, %s', print_r($data, true)));
                            }
                            if (!is_array($variant)) {
                                throw new RuntimeException(sprintf('Должен быть ассоциативный массив, %s', print_r($variant, true)));
                            }
                            if (!empty($variant['name'])) {
                                $listener($variant);
                            }
                        }
                    }
                    elseif (!empty($data['name'])) {
                        $listener($data);
                    }
                } 

                $pricelist->disconnectWorksheets();
                unset($pricelist);               
            }
        }
    }

    /**
     * Find fields
     * 
     * @param Worksheet $sheet  
     * @param int $row    
     * @param array $fields
     * 
     * @return array
     */
    protected function findFields(Worksheet $sheet, int $row, array $fields)
    {
        $titles = [];
        for ($column = 0; $column < self::HEADERS_MAX_COLUMN; $column++) {
            $cell = $sheet->getCellByColumnAndRow($column, $row);
            
            $value = $cell->getValue();
            if (is_object($value)) {
                $value = $value->getPlainText();
            }
            else {
                $value = $cell->getCalculatedValue();
            }

            $titles[] = $this->clearValue($value, true);
        }

        $found = [];
        $foundEx = [];
        foreach ($fields as $field => $title) {
            if (is_array($title)) {
                foreach ($title as $t) {
                    $key = array_search($this->clearValue($t, true), $titles);
                    if (false !== $key) {
                        $found[$field] = $key;
                        break;
                    }
                }
            }
            elseif (is_string($title)) {
                $key = array_search($this->clearValue($title, true), $titles);
                if (false !== $key) {
                    $found[$field] = $key;
                }
            }
            else {
                $foundEx[$field] = $key + $title;
            }
        }
        if (!empty($found)) {
            $found = array_merge($found, $foundEx);
        }

        return $found;    
    }

    /**
     * Read row data
     * 
     * @param Worksheet $sheet  
     * @param int $row    
     * @param array $fields 
     * 
     * @return array                     
     */
    protected function readData(Worksheet $sheet, int $row, array $fields)
    {
        $data = [];
        foreach ($fields as $field => $column) {
            $cell = $sheet->getCellByColumnAndRow($column, $row);
            if ($cell->getDataType() == DataType::TYPE_FORMULA) {
                $value = $cell->getOldCalculatedValue();
            }
            else {
                $value = $cell->getValue();
                if (is_object($value)) {
                    $value = $value->getPlainText();
                }
            }

            $data[$field] = $this->clearValueSpecial($field, $value);
        }

        return $this->strategy->processData($data, $sheet, $row, $fields);
    }

    /**
     * Clear string value
     * 
     * @param string $value           
     * @param boolean $toLowerCase     
     * @param boolean $leaveWhitespace 
     * 
     * @return string                   
     */
    protected function clearValue($value, bool $toLowerCase = false, bool $leaveWhitespace = false)
    {
        $value = preg_replace(["/[\r|\n|\t|\v|\f|\s]/uD", '/\"+/uD'], [' ', '"'], $value);

        if ($toLowerCase) {
            $value = mb_strtolower($value, 'UTF-8');
        }

        if (!$leaveWhitespace) {
            $value = trim(preg_replace('/\s+/uD', ' ', $value));
        }

        return $value;
    }

    /**
     * Clear description & url
     * 
     * @param string $field 
     * @param string $value 
     * 
     * @return string        
     */
    protected function clearValueSpecial($field, $value)
    {
        if ('description' != $field) {
            $value = $this->clearValue($value, false, $this->strategy->getLeaveWhitespace());
            if ('url' != $field) {
                $value = str_replace('_', ' ', $value);
            }
        }

        return $value;
    }
}