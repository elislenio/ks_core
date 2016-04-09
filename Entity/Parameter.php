<?php
namespace Ks\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * 
 * @ORM\Entity
 * @ORM\Table(name="ks_parameter")
 * @ORM\Entity(repositoryClass="Ks\CoreBundle\Entity\ParameterRepository")
 * @Gedmo\Loggable
 */
class Parameter
{
	/**
     * @ORM\Id
     * @ORM\Column(type="string", length=30)
	 * @Gedmo\Versioned
     * @Assert\Type(type="string", groups={"create"})
	 * @Assert\NotBlank(groups={"create"})
     */
    private $id;
	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
	 * @Gedmo\Versioned
	 * @Assert\Type(type="string", groups={"create", "update"})
	 * @Assert\NotBlank(groups={"create", "update"})
     */
    private $value;
	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
	 * @Gedmo\Versioned
	 * @Assert\Type(type="string", groups={"create", "update"})
	 */
    private $description;
	
	/**
     * @ORM\Column(type="datetime")
	 * @Gedmo\Timestampable(on="create")
	 * @Gedmo\Versioned
	 */
    private $created;
	
	/**
     * @ORM\Column(type="datetime")
	 * @Gedmo\Timestampable(on="update")
	 * @Gedmo\Versioned
	 */
    private $updated;
	
	/**
     * Constructor
     *
     */
    public function __construct()
    {
    }
	
	public function normalizeId()
	{
		$this->id = strtoupper($this->id);
	}

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Parameter
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Parameter
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Parameter
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Parameter
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Parameter
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
