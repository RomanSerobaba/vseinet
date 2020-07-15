<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParserDetailGroup.
 *
 * @ORM\Table(name="parser_detail_group")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParserDetailGroupRepository")
 */
class ParserDetailGroup
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
     * @ORM\Column(name="partner_category_id", type="integer")
     */
    private $partnerCategoryId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

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
     * Set partnerCategoryId.
     *
     * @param int $partnerCategoryId
     *
     * @return PartnerDetailGroup
     */
    public function setPartnerCategoryId($partnerCategoryId)
    {
        $this->partnerCategoryId = $partnerCategoryId;

        return $this;
    }

    /**
     * Get partnerCategoryId.
     *
     * @return int
     */
    public function getPartnerCategoryId()
    {
        return $this->partnerCategoryId;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return ParserDetailGroup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
