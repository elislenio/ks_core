<?php
namespace Ks\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MenuItemRepository
 *
 */
class MenuItemRepository extends EntityRepository
{
	public function getRootItem($menu_id)
    {
		$item = $this->createQueryBuilder('a')
            ->where('a.menu_id = :menu_id')
			->andWhere('a.parent_id is null')
            ->setParameter('menu_id', $menu_id)
            ->getQuery()
            ->getOneOrNullResult();
		
        return $item;
	}
}
