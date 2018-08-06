<?php 

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Enum extends Constraint
{
    public $ref;
    public $strict = false;
    public $multiple = false;
    public $message = 'The value you selected is not a valid enum constant.';

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'ref';
    }
}
