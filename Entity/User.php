<?php
namespace Ks\CoreBundle\Entity;

use Ks\CoreBundle\Entity\Role;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AdvancedUserInterface: We set a custom preAuth and postAuth in UserChecker
 * 
 * @ORM\Entity
 * @ORM\Table(name="ks_user")
 * @ORM\Entity(repositoryClass="Ks\CoreBundle\Entity\UserRepository")
 * @Gedmo\Loggable
 */
class User implements AdvancedUserInterface, \Serializable
{
	/**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
	 * @Gedmo\Versioned
     */
    private $id;
	
	/**
     * @ORM\Column(type="string", length=25, unique=true)
	 * @Gedmo\Versioned
	 * @Assert\NotBlank(groups={"create", "update"})
	 */
    private $username;
	
	/**
     * @ORM\Column(type="string", length=64, nullable=true)
	 */
    private $password;
	
	/**
     * @ORM\Column(type="string", length=60, unique=true)
	 * @Gedmo\Versioned
	 * @Assert\Email(groups={"create", "update"})
	 * @Assert\NotBlank(groups={"create", "update"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
	 * @Gedmo\Versioned
	 * @Assert\Type(type="string", groups={"create", "update"})
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
	 * @Gedmo\Versioned
     * @Assert\Type(type="string", groups={"create", "update"})
	 */
    private $last_name;

    /**
	 * @ORM\Column(type="boolean")
	 * @Gedmo\Versioned
     */
    private $enabled;
	
	/**
	 * @ORM\Column(type="boolean")
	 * @Gedmo\Versioned
     */
    private $locked;
	
	/**
     * @ORM\Column(type="boolean")
	 * @Gedmo\Versioned
     */
    private $password_expired;
	
	/**
     * @ORM\Column(type="string", length=25)
	 * @Gedmo\Versioned
     */
    private $salt;
	
	/**
     * @ORM\OneToMany(targetEntity="UserRole", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=TRUE)
	 */
    private $roles;
	
	/**
     * @ORM\Column(type="string", length=64, nullable=true)
	 * @Assert\NotBlank(groups={"create_local", "pwdreset"})
	 */
    private $generated_password;
	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
	 * @Gedmo\Versioned
	 */
    private $picture;
	
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
        $this->salt = md5(uniqid(null, true));
		$this->enabled = true;
		$this->locked = false;
		$this->password_expired = true;
		//$this->roles = new ArrayCollection();
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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;

        return $this;
    }
	
	/**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    
    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }
	
	/**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
	
    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }
	
    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }
	
    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
	
	/**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return User
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }
	
	/**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }
	
	/**
     * Set locked
     *
     * @param boolean $locked
     *
     * @return User
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }
	
	/**
     * Set passwordExpired
     *
     * @param boolean $passwordExpired
     *
     * @return User
     */
    public function setPasswordExpired($passwordExpired)
    {
        $this->password_expired = $passwordExpired;

        return $this;
    }

    /**
     * Get passwordExpired
     *
     * @return boolean
     */
    public function getPasswordExpired()
    {
        return $this->password_expired;
    }
	
	/**
	 * Not implemented
     */
    public function isAccountNonExpired()
    {
        return true;
    }
	
    /**
	 * Locked users cannot login until be unlocked by admin
     */
    public function isAccountNonLocked()
    {
        return ! $this->locked;
    }

    /**
	 * Users must change their expired credential
     */
    public function isCredentialsNonExpired()
    {
        return ! $this->password_expired;
    }

    /**
	 * Disabled users cannot login until be enabled by himself or by admin
     */
    public function isEnabled()
    {
		return $this->enabled;
    }
	
	/**
	 * Not implemented
     */
    public function eraseCredentials()
    {
    }
	
	/**
     * Set generated_password
     *
     * @param string $generated_password
     *
     * @return User
     */
    public function setGeneratedPassword($generated_password)
    {
        $this->generated_password = $generated_password;

        return $this;
    }
	
	/**
     * Get generated_password
     *
     * @return string
     */
    public function getGeneratedPassword()
    {
        return $this->generated_password;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
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
     * @return User
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
     * Add role
     *
     * @param Role $role
     *
     * @return User
     */
    public function addRole(Role $role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Remove role
     *
     * @param Role $role
     */
    public function removeRole(Role $role)
    {
        $this->roles->removeElement($role);
    }
	
	/**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
		$roles = array();
		
		foreach ($this->roles->getValues() as $r)
			$roles[] = $r->getRole()->getId();
			
        return $roles;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
		return serialize(array(
            $this->id,
            $this->username,
            $this->enabled,
			$this->locked
			//$this->roles,
			//$this->password,
			//$this->password_expired,
            //$this->salt
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
		list (
            $this->id,
            $this->username,
            $this->enabled,
			$this->locked
			//$this->roles,
			//$this->password,
            //$this->password_expired,
            //$this->salt
        ) = unserialize($serialized);
    }

    /**
     * Set picture
     *
     * @param string $picture
     * @return User
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    
        return $this;
    }

    /**
     * Get picture
     *
     * @return string 
     */
    public function getPicture()
    {
        return $this->picture;
    }
}