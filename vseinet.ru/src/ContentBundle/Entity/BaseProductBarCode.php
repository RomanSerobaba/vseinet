<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * BaseProductBarCode
 *
 * @ORM\Table(name="base_product_bar_code")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\BaseProductBarCodeRepository")
 */
class BaseProductBarCode
{
    // <editor-fold defaultstate="collapsed" desc="Типы postgres">    
    
    /**
     * Enum of barcode type
     *   'EAN-13'     13 цифр
     *   'EAN-13+2'   13 и 2 дополнительных цифры
     *   'EAN-13+5'   13 и 5 дополнительных цифр
     *   'EAN-8'      8 цифр
     *   'EAN-8+2'    8 и 2 дополнительных цифры
     *   'EAN-8+5'    8 и 5 дополнительных цифр
     *   'code 128'   Для кодирования всех 128 символов ASCII предусмотрено три комплекта символов штрихового кода Code 128 — A, B и C, которые могут использоваться внутри одного штрихкода.
     *                A - символы в формате ASCII от 32 до 127 (цифры от «0» до «9», буквы от «A» до «Z»), специальные символы и символы FNC 1-4,
     *                B - символы в формате ASCII от 32 до 127 (цифры от «0» до «9», буквы от «A» до «Z» и от «a» до «z»), специальные символы и символы FNC 1-4,
     *                C - числа от 00 до 99 (двузначное число кодируется одним символом) и символы FNC 1.
     */
    const barCodeTypes = [
        'EAN-13'     => 'EAN-13',
        'EAN-13+2'   => 'EAN-13+2',
        'EAN-13+5'   => 'EAN-13+5',
        'EAN-8'      => 'EAN-8',
        'EAN-8+2'    => 'EAN-8+2',
        'EAN-8+5'    => 'EAN-8+5',
        'code 128'   => 'code 128'
    ];
    
    // </editor-fold>    

    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int|null
     * @ORM\Column(name="base_product_id", type="integer", nullable=true)
     */
    private $baseProductId;

    /**
     * @var int|null
     * @ORM\Column(name="goods_pallet_id", type="integer", nullable=true)
     */
    private $goodsPalletId;

    /**
     * @var string
     * @ORM\Column(name="bar_code", type="string")
     */
    private $barCode;
    
    /**
     * @var string
     * @ORM\Column(name="bar_code_type", type="string")
     */
    private $barCodeType;
    
    // </editor-fold>
    
    // <editor-fold    defaultstate="collapsed" desc="Методы">    
    
     /**
     * Получить иденификатор
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

     /**
     * Получить идентификтор товара
     *
     * @return int|null
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Set baseProductId
     * 
     * @param int|null $baseProductId
     * 
     * @return $BaseProductBarCode
     */
    public function setBaseProductId($baseProductId = null)
    {
        return $this->baseProductId = $baseProductId;
        return $this;
    }

     /**
     * Получить идентификтор паллеты
     *
     * @return int|null
     */
    public function getGoodsPalletId()
    {
        return $this->goodsPalletId;
    }

    /**
     * Set baseProductId
     * 
     * @param int|null $goodsPalletId
     * 
     * @return $BaseProductBarCode
     */
    public function setGoodsPalletId($goodsPalletId = null)
    {
        return $this->goodsPalletId = $goodsPalletId;
        return $this;
    }

    /**
     * Get barCode
     *
     * @return string
     */
    public function getBarCode()
    {
        return $this->barCode;
    }

    /**
     * Set barCode
     * 
     * @param string $barCode
     * 
     * @return $BaseProductBarCode
     */
    public function setBarCode($barCode)
    {
        return $this->barCode = $barCode;
        return $this;
    }

    /**
     * Get barCodeType
     *
     * @return string
     */
    public function getBarCodeType()
    {
        return $this->barCodeType;
    }

    /**
     * Set barCodeType (check from ProductBarCode::barCodeTypes)
     * @param string $barCodeType
     * @return $BaseProductBarCode
     */
    public function setBarCodeType($barCodeType)
    {
        if (!$barCodeType) throw new BadRequestHttpException('Не указан тип штрихкода');
        if (!BaseProductBarCode::barCodeTypes[$barCodeType]) throw new BadRequestHttpException('Неверный тип штрихкода');
        return $this->barCodeType = BaseProductBarCode::barCodeTypes[$barCodeType];
        return $this;
    }

    public static function calcBarCodeType($barCode)
    {
        $ret = BaseProductBarCode::barCodeTypes['code 128'];
        
        if (preg_match("/^[0-9]{8}$/", $barCode)) {
            $ret = BaseProductBarCode::barCodeTypes['EAN-8'];
        }elseif (preg_match("/^[0-9]{8} [0-9]{2}$/", $barCode)) {
            $ret = BaseProductBarCode::barCodeTypes['EAN-8+2'];
        }elseif (preg_match("/^[0-9]{8} [0-9]{5}$/", $barCode)) {
            $ret = BaseProductBarCode::barCodeTypes['EAN-8+5'];
        }elseif (preg_match("/^[0-9]{13}$/", $barCode)) {
            $ret = BaseProductBarCode::barCodeTypes['EAN-13'];
        }elseif (preg_match("/^[0-9]{13} [0-9]{2}$/", $barCode)) {
            $ret = BaseProductBarCode::barCodeTypes['EAN-13+2'];
        }elseif (preg_match("/^[0-9]{13} [0-9]{5}$/", $barCode)) {
            $ret = BaseProductBarCode::barCodeTypes['EAN-13+5'];
        }
        
        return $ret;
    }
    
    // </editor-fold>
}

