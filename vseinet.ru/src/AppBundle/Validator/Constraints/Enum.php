<?php 

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Choice;

/**
 * @Annotation
 */
class Enum extends Choice
{
    public $ref;
    public $strict = true;
    public $message = 'The value you selected is not a valid enum constant.';

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'ref';
    }
}
