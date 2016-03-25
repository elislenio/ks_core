<?php
namespace Ks\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Ks\CoreBundle\Classes\DbAbs;

/**
 * MenuRepository
 *
 */
class MenuRepository extends EntityRepository
{
	public function getBranchList($menu_id)
    {
		$conn = $this->getEntityManager()->getConnection();
		
		$qb = $conn->createQueryBuilder()
			->select('a.id, a.label')
			->from('ks_menu_item', 'a')
			->andWhere('a.menu_id = :menu_id')
			->andWhere('a.is_branch = 1')
			->setParameter('menu_id', $menu_id);
		
		$records = $qb->execute()->fetchAll();
		
		// DB portability
		$engine = DbAbs::getDbEngine($conn);
		$records = DbAbs::setCase($engine, $records);
		
		return $records;
	}
}
