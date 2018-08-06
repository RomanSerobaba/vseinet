<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Annotation as VIA;
use AppBundle\Bus\Message\Message;
use OrgBundle\Bus\Work\Command\Schema\EmployeePayment;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

class ExpensesForSalaryCommand extends Message
{
    /**
     * @var EmployeePayment[]
     *
     * @VIA\Description("List of payments")
     * @Assert\Count(min=1, minMessage="You must specify at least one payment")
     * @Assert\All({
     *      @Assert\Type(type="OrgBundle\Bus\Work\Command\Schema\EmployeePayment")
     * })
     * @Assert\NotBlank(message="Payments must be specified")
     */
    public $payments;


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
                if ($property == 'payments') {
                    $payments = [];
                    if (is_array($value) && (count($value) > 0)) {
                        if (isset($value[0])) {
                            foreach ($value as $relArr) {
                                $payment = new EmployeePayment();
                                foreach ($relArr as $relKey => $relVal) {
                                    if (property_exists($payment, $relKey)) {
                                        if ($relKey == 'date') {
                                            $payment->$relKey = ($relVal instanceof \DateTime) ? $relVal : new \DateTime($relVal);
                                        } else {
                                            $payment->$relKey = $relVal;
                                        }
                                    }
                                }
                                $payments[] = $payment;
                            }
                        } else {
                            $payment = new EmployeePayment();
                            foreach ($value as $relKey => $relVal) {
                                if (property_exists($payment, $relKey)) {
                                    if ($relKey == 'date') {
                                        $payment->$relKey = ($relVal instanceof \DateTime) ? $relVal : new \DateTime($relVal);
                                    } else {
                                        $payment->$relKey = $relVal;
                                    }
                                }
                            }
                            $payments[] = $payment;
                        }
                    } else {
                        throw new InvalidArgumentException('Invalid format of command');
                    }
                    $value = $payments;
                }
                $this->$property = $value;
            }
        }
    }
}