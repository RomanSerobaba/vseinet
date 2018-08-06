<?php

namespace OrgBundle\Bus\Employee\Command;

use AppBundle\Annotation as VIA;
use AppBundle\Bus\Message\Message;
use OrgBundle\Bus\Employee\Command\Schema\Relative;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateRelativesCommand extends Message
{
    /**
     * @var int
     *
     * @VIA\Description("Employee Id")
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     */
    public $id;

    /**
     * @var Relative[]
     *
     * @VIA\Description("List of relatives")
     * @Assert\Count(min=1, minMessage="You must specify at least one relative")
     * @Assert\All({
     *      @Assert\Type(type="OrgBundle\Bus\Employee\Command\Schema\Relative")
     * })
     * @Assert\NotBlank(message="Relatives must be specified")
     */
    public $relatives;


    /**
     * Initial object with values from array.
     *
     * @param array $values
     * @param array $extra
     */
    public function __construct(array $values = [], array $extra = [])
    {
        $data = $this->empty2null($extra + $values);

        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                if ($property == 'relatives') {
                    $relatives = [];
                    if (is_array($value) && (count($value) > 0)) {
                        if (isset($value[0])) {
                            foreach ($value as $relArr) {
                                $relative = new Relative();
                                foreach ($relArr as $relKey => $relVal) {
                                    if (property_exists($relative, $relKey)) {
                                        $relative->$relKey = $relVal;
                                    }
                                }
                                $relatives[] = $relative;
                            }
                        } else {
                            $relative = new Relative();
                            foreach ($value as $relKey => $relVal) {
                                if (property_exists($relative, $relKey)) {
                                    $relative->$relKey = $relVal;
                                }
                            }
                            $relatives[] = $relative;
                        }
                    } else {
                        throw new InvalidArgumentException('Invalid format of command');
                    }
                    $value = $relatives;
                }
                $this->$property = $value;
            }
        }
    }
}