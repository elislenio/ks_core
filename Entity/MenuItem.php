<?php
namespace Ks\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Ks\CoreBundle\Entity\Menu;
use Ks\CoreBundle\Entity\AccessControl;

/**
 * 
 * @ORM\Entity
 * @ORM\Table(name="ks_menu_item")
 * @ORM\Entity(repositoryClass="Ks\CoreBundle\Entity\MenuItemRepository")
 * @Gedmo\Loggable
 */
class MenuItem
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
     * @Assert\NotBlank(groups={"create"})
     */
    private $menu_id;
	
	/**
     * @ORM\Column(type="string", length=255)
	 * @Gedmo\Versioned
	 * @Assert\Type(type="string", groups={"create", "update"})
	 * @Assert\NotBlank(groups={"create", "update"})
     */
    private $label;
	
	/**
     * @ORM\Column(type="integer", nullable=true)
	 * @Gedmo\Versioned
     * @Assert\NotBlank(groups={"create", "update"})
     */
    private $parent_id;
	
	/**
     * @ORM\Column(type="integer")
	 * @Gedmo\Versioned
     * @Assert\NotBlank(groups={"create", "update"})
     */
    private $item_order;
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 * @Gedmo\Versioned
	 */
    private $is_branch;
	
	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 * @Gedmo\Versioned
	 */
    private $visible;
	
	/**
     * @ORM\Column(type="string", length=64, nullable=true)
	 * @Gedmo\Versioned
	 * @Assert\Type(type="string", groups={"create", "update"})
	 */
    private $route;
	
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
     * @ORM\ManyToOne(targetEntity="Menu")
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id", onDelete="CASCADE", nullable=FALSE)
	 */
    private $menu;
	
	/**
     * @ORM\ManyToOne(targetEntity="MenuItem")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE", nullable=FALSE)
	 */
    private $parent;
	
	/**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Gedmo\Versioned
	 */
    private $ac_id;
	
	/**
     * @ORM\ManyToOne(targetEntity="AccessControl")
     * @ORM\JoinColumn(name="ac_id", referencedColumnName="id", onDelete="RESTRICT", nullable=FALSE)
	 */
    private $ac;
	
	/**
     * @ORM\Column(type="integer", nullable=true)
	 * @Gedmo\Versioned
	 */
    private $mask;
	
	/**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
	 * @Assert\Type(type="string", groups={"create", "update"})
	 */
    private $icon;
	
	/**
     * Constructor
     *
     */
    public function __construct()
    {
    }

	/**
     * @Assert\Callback(groups={"create", "update"})
     */
	public function validateMask(ExecutionContextInterface $context)
    {
        if ($this->ac_id and empty($this->mask)) {
			$context->buildViolation('Si selecciona un valor de funcion debe seleccionar un permiso.')
                ->atPath('mask')
                ->addViolation();
			return;
        }
		
		if ($this->mask and empty($this->ac_id)) {
			$context->buildViolation('Si selecciona un valor de permiso debe seleccionar una funcion.')
                ->atPath('ac_id')
                ->addViolation();
        }
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
     * Set menu_id
     *
     * @param string $menuId
     * @return MenuItem
     */
    public function setMenuId($menuId)
    {
        $this->menu_id = $menuId;
    
        return $this;
    }

    /**
     * Get menu_id
     *
     * @return string 
     */
    public function getMenuId()
    {
        return $this->menu_id;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return MenuItem
     */
    public function setLabel($label)
    {
        $this->label = $label;
    
        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set menu
     *
     * @param Menu $menu
     * @return MenuItem
     */
    public function setMenu(Menu $menu)
    {
        $this->menu = $menu;
    
        return $this;
    }

    /**
     * Get menu
     *
     * @return Menu 
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set parent_id
     *
     * @param integer $parentId
     * @return MenuItem
     */
    public function setParentId($parentId)
    {
        $this->parent_id = $parentId;
    
        return $this;
    }

    /**
     * Get parent_id
     *
     * @return integer 
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Set parent
     *
     * @param MenuItem $parent
     * @return MenuItem
     */
    public function setParent(MenuItem $parent)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return MenuItem 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set item_order
     *
     * @param integer $itemOrder
     * @return MenuItem
     */
    public function setItemOrder($itemOrder)
    {
        $this->item_order = $itemOrder;
    
        return $this;
    }

    /**
     * Get item_order
     *
     * @return integer 
     */
    public function getItemOrder()
    {
        return $this->item_order;
    }

    /**
     * Set is_branch
     *
     * @param boolean $isBranch
     * @return MenuItem
     */
    public function setIsBranch($isBranch)
    {
        $this->is_branch = $isBranch;
    
        return $this;
    }

    /**
     * Get is_branch
     *
     * @return boolean 
     */
    public function getIsBranch()
    {
        return $this->is_branch;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return MenuItem
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set route
     *
     * @param string $route
     * @return MenuItem
     */
    public function setRoute($route)
    {
        $this->route = $route;
    
        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set ac_id
     *
     * @param string $acId
     * @return MenuItem
     */
    public function setAcId($acId)
    {
        $this->ac_id = $acId;
    
        return $this;
    }

    /**
     * Get ac_id
     *
     * @return string 
     */
    public function getAcId()
    {
        return $this->ac_id;
    }

    /**
     * Set mask
     *
     * @param integer $mask
     * @return MenuItem
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
     * Set ac
     *
     * @param AccessControl $ac
     * @return MenuItem
     */
    public function setAc(AccessControl $ac)
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
     * Set icon
     *
     * @param string $icon
     * @return MenuItem
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    
        return $this;
    }

    /**
     * Get icon
     *
     * @return string 
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return MenuItem
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
     * @return MenuItem
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
