<?php 

namespace ReservesBundle\Bus\GoodsIssueDoc\Command\Schema;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;
//use Symfony\Component\Validator\ExecutionContext;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProductSchema extends Message
{    

    /**
     * @VIA\Description("Идентификатор продукта")
     * @Assert\NotBlank(message="Идентификатор продукта должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;
    
    /**
     * @VIA\Description("Идентификатор заказа клиента")
     * @Assert\Type(type="integer")
     */
    public $orderItemId;
    
    /**
     * @VIA\Description("Идентификатор заказа поставщику")
     * @Assert\NotBlank(message="Идентификатор заказа поставщику должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $supplyItemId;
    
    /**
     * @VIA\Description("Тип состояния товара")
     * @Assert\NotBlank(message="Тип дефекта должен быть указан")
     * @Assert\Type(type="string")
     */
    public $goodsStateCode;
    
    /**
     * @VIA\Description("Количество продукта")
     * @Assert\NotBlank(message="Количество продукта должно быть указано")
     * @Assert\Type(type="integer")
     */
    public $quantity;
    
    public static function validate($object, ExecutionContextInterface $context, $payload)
    {

        if (!array_key_exists('baseProductId', $object)) {
            
            $context->buildViolation("Не указан идентификатор товара.")->addViolation();
            
        }elseif (!filter_var($object['baseProductId'], FILTER_VALIDATE_INT)) {
            
            $context->buildViolation("Некорректный идентификатор товара.")->addViolation();
            
        }elseif (!array_key_exists('orderItemId', $object)) {
            
            $context->buildViolation("Не указан идентификатор заказа.")->addViolation();
            
        }elseif (!filter_var($object['orderItemId'], FILTER_VALIDATE_INT) && !empty($object['orderItemId'])) {
            
            $context->buildViolation("Некорректный идентификатор заказа.")->addViolation();
            
        }elseif (!array_key_exists('supplyItemId', $object)) {
            
            $context->buildViolation("Не указан идентификатор партии.")->addViolation();
            
        }elseif (!filter_var($object['supplyItemId'], FILTER_VALIDATE_INT)) {
            
            $context->buildViolation("Некорректный идентификатор партии.")->addViolation();

        }elseif (!array_key_exists('quantity', $object)) {
            
            $context->buildViolation("Не указано количество.")->addViolation();
            
        }elseif ((!filter_var($object['quantity'], FILTER_VALIDATE_INT))||(0 == $object['quantity'])) {
            
            $context->buildViolation("Некорректное количество.")->addViolation();

        }elseif (empty($object['goodsStateCode'])) {
            
            $context->buildViolation("Не указано состояние товара.")->addViolation();
            
        }

    }
    
}
