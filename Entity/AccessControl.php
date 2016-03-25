<?php
namespace Ks\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Ks\CoreBundle\Entity\AccessControlList;

/**
 * @ORM\Entity
 * @ORM\Table(name="ks_ac")
 * @ORM\Entity(repositoryClass="Ks\CoreBundle\Entity\ACRepository")
 * @Gedmo\Loggable
 */
class AccessControl
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
     * @ORM\Column(type="string", length=255)
	 * @Gedmo\Versioned
	 * @Assert\Type(type="string", groups={"create", "update"})
	 * @Assert\NotBlank(groups={"create", "update"})
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
     * @ORM\OneToMany(targetEntity="AccessControlList", mappedBy="ac", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    protected $acl;
	
	/**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return AccessControl
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
     * @return AccessControl
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
     * Set created
     *
     * @param \DateTime $created
     * @return AccessControl
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
     * @return AccessControl
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

    /**
     * Add acl
     *
     * @param AccessControlList $acl
     *
     * @return AccessControl
     */
    public function addAcl(AccessControlList $acl)
    {
        $this->acl[] = $acl;

        return $this;
    }

    /**
     * Remove acl
     *
     * @param AccessControlList $acl
     */
    public function removeAcl(AccessControlList $acl)
    {
        $this->acl->removeElement($acl);
    }

    /**
     * Get acl
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAcl()
    {
        return $this->acl;
    }
	
	public function normalizeId()
	{
		$this->id = strtoupper($this->id);
	}
}
