<?php
namespace Ks\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Ks\CoreBundle\Entity\User;
use Ks\CoreBundle\Entity\Role;

/**
 * @ORM\Entity
 * @ORM\Table(name="ks_user_role", uniqueConstraints={@ORM\UniqueConstraint(name="user_role_uk", columns={"user_id", "role_id"})})
 * @ORM\Entity(repositoryClass="Ks\CoreBundle\Entity\UserRoleRepository")
 * @Gedmo\Loggable
 */
class UserRole
{
	/**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
	 * @Gedmo\Versioned
     */
    private $id;
	
	/**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned
	 * @Assert\Type(type="integer", groups={"create"})
	 * @Assert\NotBlank(groups={"create"})
     */
    private $user_id;
	
	/**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="roles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=FALSE)
	 */
    private $user;
	
	/**
     * @ORM\Column(type="string", length=30)
	 * @Gedmo\Versioned
     * @Assert\Type(type="string", groups={"create"})
	 * @Assert\NotBlank(groups={"create"})
     */
    private $role_id;
	
	/**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="users")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", onDelete="CASCADE", nullable=FALSE)
	 */
    private $role;
	
	/**
     * @ORM\Column(type="datetime")
	 * @Gedmo\Timestampable(on="create")
	 * @Gedmo\Versioned
	 */
    private $assigned;
	
	
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
     * Set user_id
     *
     * @param integer $userId
     * @return UserRole
     */
    public function setUserId($userId)
    {
        $this->user_id = (int) $userId;
    
        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set role_id
     *
     * @param string $roleId
     * @return UserRole
     */
    public function setRoleId($roleId)
    {
        $this->role_id = $roleId;
    
        return $this;
    }

    /**
     * Get role_id
     *
     * @return string 
     */
    public function getRoleId()
    {
        return $this->role_id;
    }

    /**
     * Set assigned
     *
     * @param \DateTime $assigned
     * @return UserRole
     */
    public function setAssigned($assigned)
    {
        $this->assigned = $assigned;
    
        return $this;
    }

    /**
     * Get assigned
     *
     * @return \DateTime 
     */
    public function getAssigned()
    {
        return $this->assigned;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return UserRole
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set role
     *
     * @param Role $role
     * @return UserRole
     */
    public function setRole(Role $role)
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
}
