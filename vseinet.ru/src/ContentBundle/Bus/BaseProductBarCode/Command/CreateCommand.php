<?php 

namespace ContentBundle\Bus\BaseProductBarCode\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{    
    /**
     * @VIA\Description("Штрихкод")
     * @Assert\NotBlank(message="Значение штрихкода не может быть пустым")
     * @Assert\Type(type="string")
     */
    public $barCode;

    /**
     * @VIA\Description("Тип штрихкода:
     *   'EAN-13'     13 цифр
     *   'EAN-13+2'   13 и 2 дополнительных цифры
     *   'EAN-13+5'   13 и 5 дополнительных цифр
     *   'EAN-8'      8 цифр
     *   'EAN-8+2'    8 и 2 дополнительных цифры
     *   'EAN-8+5'    8 и 5 дополнительных цифр
     *   'code 128'   Для кодирования всех 128 символов ASCII предусмотрено три комплекта символов штрихового кода Code 128 — A, B и C, которые могут использоваться внутри одного штрихкода.
     *                A - символы в формате ASCII от 32 до 127 (цифры от «0» до «9», буквы от «A» до «Z»), специальные символы и символы FNC 1-4,
     *                B - символы в формате ASCII от 32 до 127 (цифры от «0» до «9», буквы от «A» до «Z» и от «a» до «z»), специальные символы и символы FNC 1-4,
     *                C - числа от 00 до 99 (двузначное число кодируется одним символом) и символы FNC 1.")
     * @Assert\Choice({"EAN-13", "EAN-13+2", "EAN-13+5", "EAN-8", "EAN-8+2", "EAN-8+5", "code 128"}, strict=true)
     */
    public $barCodeType;

    /**
     * @VIA\Description("Идентификатор продукции")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @VIA\Description("Идентификатор паллета")
     * @Assert\Type(type="integer")
     */
    public $goodsPalletId;

}