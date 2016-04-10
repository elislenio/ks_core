<?php
namespace Ks\CoreBundle\Services;

use Doctrine\ORM\EntityManager;
use Ks\CoreBundle\Form\Type\MenuCreateType;
use Ks\CoreBundle\Form\Type\MenuEditType;
use Ks\CoreBundle\Form\Type\MenuItemCreateType;
use Ks\CoreBundle\Form\Type\MenuItemEditType;
use Ks\CoreBundle\Entity\Menu;
use Ks\CoreBundle\Entity\MenuItem;
use Ks\CoreBundle\Entity\AccessControl;

/**
 * MenuPersist
 *
 */
class MenuModel
{
	private $em;
	private $form_factory;
	
	public function __construct(EntityManager $em, $form_factory)
    {
		$this->em = $em;
		$this->form_factory = $form_factory;
    }
	
	public function get($id)
	{
		return $this->em->getRepository('KsCoreBundle:Menu')->find($id);
	}
	
	public function getItem($id)
	{
		return $this->em->getRepository('KsCoreBundle:MenuItem')->find($id);
	}
	
	public function insert($menu)
	{
		// Creates the menu
		$this->em->persist($menu);
		
		// Creates the root item
		$root_item = new MenuItem();
		$root_item->setMenuId($menu->getId())
			->setLabel('Root')
			->setMenu($menu)
			->setItemOrder(1)
			->setIsBranch(true)
			->setVisible(false);
		
		$this->em->persist($root_item);
		$this->em->flush();
	}
	
	public function update($menu)
	{
		$this->em->persist($menu);
		$this->em->flush();
	}
	
	public function delete($menu)
	{
		$this->em->remove($menu);
		$this->em->flush();
	}
	
	public function insertItem($menu, $menu_item)
	{
		$menu_item->setMenu($menu);
		
		$parent = $this->em->getRepository('KsCoreBundle:MenuItem')->find($menu_item->getParentId());
		$menu_item->setParent($parent);
		
		if ($menu_item->getAcId())
		{
			$ac = $this->em->getRepository('KsCoreBundle:AccessControl')->find($menu_item->getAcId());
			$menu_item->setAc($ac);
		}
		
		$this->em->persist($menu_item);
		$this->em->flush();
	}
	
	public function updateItem($menu_item)
	{
		if (!$menu_item->getAcId())	$menu_item->setAcId(null);
		if (!$menu_item->getMask())	$menu_item->setMask(null);
		
		$this->em->persist($menu_item);
		$this->em->flush();
	}
	
	public function deleteItem($menu_item)
	{
		$this->em->remove($menu_item);
		$this->em->flush();
	}
	
	public function getFormCreate($menu)
	{
		return $this->form_factory->create(MenuCreateType::class, $menu, array('validation_groups' => array('create')));
	}
	
	public function getFormEdit($menu)
	{
		return $this->form_factory->create(MenuEditType::class, $menu, array('validation_groups' => array('update')));
	}
	
	public function getFormItemCreate($menu_item)
	{
		return $this->form_factory->create(MenuItemCreateType::class, $menu_item, array('validation_groups' => array('create')));
	}
	
	public function getFormItemEdit($menu_item)
	{
		return $this->form_factory->create(MenuItemEditType::class, $menu_item, array('validation_groups' => array('update')));
	}
}
