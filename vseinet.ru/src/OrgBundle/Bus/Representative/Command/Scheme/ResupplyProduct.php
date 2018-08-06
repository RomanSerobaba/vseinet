<?php 

namespace OrgBundle\Bus\Representative\Command\Scheme;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ResupplyProduct
{    
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    public function __construct(int $id, int $quantity)
    {
        $this->id = $id;
        $this->quantity = $quantity;
    }
    
    public static function validate($object, ExecutionContextInterface $context)
    {

        if (!array_key_exists('id', $object)) {
            
            $context->buildViolation("Не указан идентификатор товара.")->addViolation();
            
        }elseif (!filter_var($object['id'], FILTER_VALIDATE_INT)) {
            
            $context->buildViolation("Некорректный идентификатор товара.")->addViolation();
            
        }elseif (!array_key_exists('quantity', $object)) {
            
            $context->buildViolation("Не указано количество товара.")->addViolation();
            
        }elseif (!filter_var($object['quantity'], FILTER_VALIDATE_INT)) {
            
            $context->buildViolation("Некорректное количество товара.")->addViolation();
            
        }
    }
}