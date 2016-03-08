<?php
namespace Ks\CoreBundle\Services;

use Doctrine\ORM\EntityManager;
use Ks\CoreBundle\Entity\Menu;
use Ks\CoreBundle\Entity\MenuItem;
use Ks\CoreBundle\Entity\AccessControl;

/**
 * MenuPersist
 *
 */
class MenuPersist
{
	private $em;
	
	public function __construct(EntityManager $em)
    {
		$this->em = $em;
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
}
