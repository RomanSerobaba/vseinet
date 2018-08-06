<?php

namespace ReservesBundle\AddOn;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TempStorage
{
    private $storeDir = '';
    
    private function getStoreDir() 
    {
        if (empty($this->storeDir)) {
            $this->storeDir = $this->storeDir = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_FILENAME']), 0, -2)). '/var/tempstore';
        }
        
        return $this->storeDir;
    }
    
    
    public function isValidUUID($uuid)
    {
      return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
                        '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
    }
  
    private function UUIDv4()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                // 16 bits for "time_mid"
                mt_rand(0, 0xffff),
                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
                mt_rand(0, 0x0fff) | 0x4000,
                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand(0, 0x3fff) | 0x8000,
                // 48 bits for "node"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * @param string $data  сериализованные данные, предположительно json
     * @param string $uuid  необязательный параметр, ключ данных
     *
     * @return string uuid, ключ данных
     */
    public function setData(string $data, string $uuid = ''): string
    {
        if (empty($uuid)) {
            $uuid = $this->UUIDv4();
        }

        $dirName = $this->getStoreDir() .'/'. substr($uuid, 0, 2);
        if (!is_dir($dirName)) {
            mkdir($this->getStoreDir() .'/'. substr($uuid, 0, 2));
        }
        
        if (file_put_contents($dirName .'/'. $uuid, $data, LOCK_EX) === false) {
            throw new HttpException(500, 'Ошибка записи во временное хранилище.');
        }
        
        return $uuid;
    }

    /**
     * @param string $uuid  ключ данных
     *
     * @return string сериализованные данные, предположительно json
     */
    public function getData(string $uuid): string
    {
        if (!$this->isValidUUID($uuid)) {
            throw new BadRequestHttpException('Некорректный ключ данных');
        }
        return file_get_contents($this->getStoreDir() .'/'. substr($uuid, 0, 2) .'/'. $uuid);
    }

}