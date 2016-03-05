<?php
namespace Ks\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * @ORM\Entity
 * @ORM\Table(name="ks_acl", uniqueConstraints={@ORM\UniqueConstraint(name="acl_cl_role_uk", columns={"ac_id", "role_id"})}, indexes={@ORM\Index(name="acl_ac_idx", columns={"ac_id"}), @ORM\Index(name="acl_role_idx", columns={"role_id"})})
 * @ORM\Entity(repositoryClass="Ks\CoreBundle\Entity\ACLRepository")
 * @Gedmo\Loggable
 */
class AccessControlList
{
	/**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
	 * @Gedmo\Versioned
     */
    private $id;
	
	/**
     * @ORM\Column(type="string", length=30)
	 * @Gedmo\Versioned
     * @Assert\Type(type="string", groups={"create"})
	 * @Assert\NotBlank(groups={"create"})
     */
    private $role_id;
	
	/**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="acl")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", onDelete="CASCADE", nullable=FALSE)
	 */
    private $role;
	
	/**
     * @ORM\Column(type="string", length=30)
     * @Gedmo\Versioned
	 * @Assert\Type(type="string", groups={"create"})
	 * @Assert\NotBlank(groups={"create"})
     */
    private $ac_id;
	
	/**
     * @ORM\ManyToOne(targetEntity="AccessControl", inversedBy="acl")
     * @ORM\JoinColumn(name="ac_id", referencedColumnName="id", onDelete="CASCADE", nullable=FALSE)
	 */
    private $ac;
	
	/**
     * @ORM\Column(type="integer")
	 * @Gedmo\Versioned
	 */
    private $mask;
	
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
     * DO NOT PERSIST
	 * @Assert\Type(type="boolean", groups={"create", "update"})
	 * @Assert\NotNull(groups={"create", "update"})
	 */
	private $mask_view;
	
	/**
     * DO NOT PERSIST
	 * @Assert\Type(type="boolean", groups={"create", "update"})
	 * @Assert\NotNull(groups={"create", "update"})
	 */
	private $mask_create;
	
	/**
     * DO NOT PERSIST
	 * @Assert\Type(type="boolean", groups={"create", "update"})
	 * @Assert\NotNull(groups={"create", "update"})
	 */
	private $mask_edit;
	
	/**
     * DO NOT PERSIST
	 * @Assert\Type(type="boolean", groups={"create", "update"})
	 * @Assert\NotNull(groups={"create", "update"})
	 */
	private $mask_delete;
	
	/**
     * Constructor
     */
    public function __construct()
    {
    }

	/**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set mask
     *
     * @param integer $mask
     *
     * @return AccessControlList
     */
    public function setMask($mask)
    {
        $this->mask = $mask;

        return $this;
    }

    /**
     * Get mask
     *
     * @return integer
     */
    public function getMask()
    {
        return $this->mask;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return AccessControlList
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
     * @return AccessControlList
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
     * Set ac
     *
     * @param AccessControl $ac
     *
     * @return AccessControlList
     */
    public function setAc($ac)
    {
        $this->ac = $ac;

        return $this;
    }

    /**
     * Get ac
     *
     * @return AccessControl
     */
    public function getAc()
    {
        return $this->ac;
    }

    /**
     * Set role
     *
     * @param Role $role
     *
     * @return AccessControlList
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Get role
     *
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set acId
     *
     * @param string $acId
     *
     * @return AccessControlList
     */
    public function setAcId($acId)
    {
        $this->ac_id = $acId;
        return $this;
    }

    /**
     * Get acId
     *
     * @return string
     */
    public function getAcId()
    {
        return $this->ac_id;
    }

    /**
     * Set roleId
     *
     * @param string $roleId
     *
     * @return AccessControlList
     */
    public function setRoleId($roleId)
    {
        $this->role_id = $roleId;

        return $this;
    }

    /**
     * Get roleId
     *
     * @return string
     */
    public function getRoleId()
    {
        return $this->role_id;
    }
	
	/**
     * Set mask_view
     *
     * @return AccessControlList
     */
    public function setMaskView($mask_view)
    {
        $this->mask_view = $mask_view;
		return $this;
    }
	
	/**
     * Get mask_view
     *
     * @return boolean
     */
    public function getMaskView()
    {
        return $this->mask_view;
    }
	
	/**
     * Set mask_create
     *
     * @return AccessControlList
     */
    public function setMaskCreate($mask_create)
    {
        $this->mask_create = $mask_create;
		return $this;
    }
	
	/**
     * Get mask_create
     *
     * @return boolean
     */
    public function getMaskCreate()
    {
        return $this->mask_create;
    }
	
	/**
     * Set mask_edit
     *
     * @return AccessControlList
     */
    public function setMaskEdit($mask_edit)
    {
        $this->mask_edit = $mask_edit;
		return $this;
    }
	
	/**
     * Get mask_edit
     *
     * @return boolean
     */
    public function getMaskEdit()
    {
        return $this->mask_edit;
    }
	
	/**
     * Set mask_delete
     *
     * @return AccessControlList
     */
    public function setMaskDelete($mask_delete)
    {
        $this->mask_delete = $mask_delete;
		return $this;
    }
	
	/**
     * Get mask_delete
     *
     * @return boolean
     */
    public function getMaskDelete()
    {
        return $this->mask_delete;
    }
	
	public function buildMask()
    {
		$builder = new MaskBuilder();
		if ($this->mask_view) $builder->add('view');
		if ($this->mask_create) $builder->add('create');
		if ($this->mask_edit) $builder->add('edit');
		if ($this->mask_delete) $builder->add('delete');
		$this->mask = $builder->get();
    }
	
	public function parseMask()
    {
		$this->mask_view 	= (boolean) (MaskBuilder::MASK_VIEW & $this->mask);
		$this->mask_create 	= (boolean) (MaskBuilder::MASK_CREATE & $this->mask);
		$this->mask_edit 	= (boolean) (MaskBuilder::MASK_EDIT & $this->mask);
		$this->mask_delete 	= (boolean) (MaskBuilder::MASK_DELETE & $this->mask);
    }
}
