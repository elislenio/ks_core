<?php
namespace Ks\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MenuRepository
 *
 */
class MenuRepository extends EntityRepository
{
	public function getBranchList($menu_id)
    {
		$qb = $this->getEntityManager()->getConnection()->createQueryBuilder()
			->select('a.id, a.label')
			->from('ks_menu_item', 'a')
			->andWhere('a.menu_id = :menu_id')
			->andWhere('a.is_branch = 1')
			->setParameter('menu_id', $menu_id);
		
		$records = $qb->execute()->fetchAll();
		
		return $records;
	}
}
