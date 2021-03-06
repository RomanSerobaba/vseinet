<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RepresentativeSchedule
 *
 * @ORM\Table(name="representative_schedule")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RepresentativeScheduleRepository")
 */
class RepresentativeSchedule
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="s1", type="time", nullable=true)
     */
    private $s1;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="t1", type="time", nullable=true)
     */
    private $t1;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="s2", type="time", nullable=true)
     */
    private $s2;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="t2", type="time", nullable=true)
     */
    private $t2;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="s3", type="time", nullable=true)
     */
    private $s3;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="t3", type="time", nullable=true)
     */
    private $t3;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="s4", type="time", nullable=true)
     */
    private $s4;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="t4", type="time", nullable=true)
     */
    private $t4;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="s5", type="time", nullable=true)
     */
    private $s5;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="t5", type="time", nullable=true)
     */
    private $t5;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="s6", type="time", nullable=true)
     */
    private $s6;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="t6", type="time", nullable=true)
     */
    private $t6;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="s7", type="time", nullable=true)
     */
    private $s7;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="t7", type="time", nullable=true)
     */
    private $t7;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer")
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;


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
     * @return RepresentativeSchedule
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
     * Set s1.
     *
     * @param \DateTime|null $s1
     *
     * @return RepresentativeSchedule
     */
    public function setS1($s1 = null)
    {
        $this->s1 = $s1;

        return $this;
    }

    /**
     * Get s1.
     *
     * @return \DateTime|null
     */
    public function getS1()
    {
        return $this->s1;
    }

    /**
     * Set t1.
     *
     * @param \DateTime|null $t1
     *
     * @return RepresentativeSchedule
     */
    public function setT1($t1 = null)
    {
        $this->t1 = $t1;

        return $this;
    }

    /**
     * Get t1.
     *
     * @return \DateTime|null
     */
    public function getT1()
    {
        return $this->t1;
    }

    /**
     * Set s2.
     *
     * @param \DateTime|null $s2
     *
     * @return RepresentativeSchedule
     */
    public function setS2($s2 = null)
    {
        $this->s2 = $s2;

        return $this;
    }

    /**
     * Get s2.
     *
     * @return \DateTime|null
     */
    public function getS2()
    {
        return $this->s2;
    }

    /**
     * Set t2.
     *
     * @param \DateTime|null $t2
     *
     * @return RepresentativeSchedule
     */
    public function setT2($t2 = null)
    {
        $this->t2 = $t2;

        return $this;
    }

    /**
     * Get t2.
     *
     * @return \DateTime|null
     */
    public function getT2()
    {
        return $this->t2;
    }

    /**
     * Set s3.
     *
     * @param \DateTime|null $s3
     *
     * @return RepresentativeSchedule
     */
    public function setS3($s3 = null)
    {
        $this->s3 = $s3;

        return $this;
    }

    /**
     * Get s3.
     *
     * @return \DateTime|null
     */
    public function getS3()
    {
        return $this->s3;
    }

    /**
     * Set t3.
     *
     * @param \DateTime|null $t3
     *
     * @return RepresentativeSchedule
     */
    public function setT3($t3 = null)
    {
        $this->t3 = $t3;

        return $this;
    }

    /**
     * Get t3.
     *
     * @return \DateTime|null
     */
    public function getT3()
    {
        return $this->t3;
    }

    /**
     * Set s4.
     *
     * @param \DateTime|null $s4
     *
     * @return RepresentativeSchedule
     */
    public function setS4($s4 = null)
    {
        $this->s4 = $s4;

        return $this;
    }

    /**
     * Get s4.
     *
     * @return \DateTime|null
     */
    public function getS4()
    {
        return $this->s4;
    }

    /**
     * Set t4.
     *
     * @param \DateTime|null $t4
     *
     * @return RepresentativeSchedule
     */
    public function setT4($t4 = null)
    {
        $this->t4 = $t4;

        return $this;
    }

    /**
     * Get t4.
     *
     * @return \DateTime|null
     */
    public function getT4()
    {
        return $this->t4;
    }

    /**
     * Set s5.
     *
     * @param \DateTime|null $s5
     *
     * @return RepresentativeSchedule
     */
    public function setS5($s5 = null)
    {
        $this->s5 = $s5;

        return $this;
    }

    /**
     * Get s5.
     *
     * @return \DateTime|null
     */
    public function getS5()
    {
        return $this->s5;
    }

    /**
     * Set t5.
     *
     * @param \DateTime|null $t5
     *
     * @return RepresentativeSchedule
     */
    public function setT5($t5 = null)
    {
        $this->t5 = $t5;

        return $this;
    }

    /**
     * Get t5.
     *
     * @return \DateTime|null
     */
    public function getT5()
    {
        return $this->t5;
    }

    /**
     * Set s6.
     *
     * @param \DateTime|null $s6
     *
     * @return RepresentativeSchedule
     */
    public function setS6($s6 = null)
    {
        $this->s6 = $s6;

        return $this;
    }

    /**
     * Get s6.
     *
     * @return \DateTime|null
     */
    public function getS6()
    {
        return $this->s6;
    }

    /**
     * Set t6.
     *
     * @param \DateTime|null $t6
     *
     * @return RepresentativeSchedule
     */
    public function setT6($t6 = null)
    {
        $this->t6 = $t6;

        return $this;
    }

    /**
     * Get t6.
     *
     * @return \DateTime|null
     */
    public function getT6()
    {
        return $this->t6;
    }

    /**
     * Set s7.
     *
     * @param \DateTime|null $s7
     *
     * @return RepresentativeSchedule
     */
    public function setS7($s7 = null)
    {
        $this->s7 = $s7;

        return $this;
    }

    /**
     * Get s7.
     *
     * @return \DateTime|null
     */
    public function getS7()
    {
        return $this->s7;
    }

    /**
     * Set t7.
     *
     * @param \DateTime|null $t7
     *
     * @return RepresentativeSchedule
     */
    public function setT7($t7 = null)
    {
        $this->t7 = $t7;

        return $this;
    }

    /**
     * Get t7.
     *
     * @return \DateTime|null
     */
    public function getT7()
    {
        return $this->t7;
    }

    /**
     * Set createdBy.
     *
     * @param int $createdBy
     *
     * @return RepresentativeSchedule
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return RepresentativeSchedule
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
