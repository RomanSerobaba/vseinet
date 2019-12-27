<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RepresentativeToPaymentType
 *
 * @ORM\Table(name="representative_to_payment_type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RepresentativeToPaymentTypeRepository")
 */
class RepresentativeToPaymentType
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="representative_id", type="integer")
     */
    private $representativeId;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_type_code", type="string", length=255)
     */
    private $paymentTypeCode;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_internal", type="boolean")
     */
    private $isInternal;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set representativeId.
     *
     * @param int $representativeId
     *
     * @return RepresentativeToPaymentType
     */
    public function setRepresentativeId($representativeId)
    {
        $this->representativeId = $representativeId;

        return $this;
    }

    /**
     * Get representativeId.
     *
     * @return int
     */
    public function getRepresentativeId()
    {
        return $this->representativeId;
    }

    /**
     * Set paymentTypeCode.
     *
     * @param string $paymentTypeCode
     *
     * @return RepresentativeToPaymentType
     */
    public function setPaymentTypeCode($paymentTypeCode)
    {
        $this->paymentTypeCode = $paymentTypeCode;

        return $this;
    }

    /**
     * Get paymentTypeCode.
     *
     * @return string
     */
    public function getPaymentTypeCode()
    {
        return $this->paymentTypeCode;
    }

    /**
     * Set isInternal.
     *
     * @param bool $isInternal
     *
     * @return RepresentativeToPaymentType
     */
    public function setIsInternal($isInternal)
    {
        $this->isInternal = $isInternal;

        return $this;
    }

    /**
     * Get isInternal.
     *
     * @return bool
     */
    public function getIsInternal()
    {
        return $this->isInternal;
    }
}
