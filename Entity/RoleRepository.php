<?php
namespace Ks\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Ks\CoreBundle\Classes\DbAbs;

/**
 * RoleRepository
 *
 */
class RoleRepository extends EntityRepository
{
	public function getAvailableControlList($role_id)
    {
		$conn = $this->getEntityManager()->getConnection();
		
		$qb = $conn->createQueryBuilder()
			->select('a.id, a.description')
			->from('ks_ac', 'a')
			->andWhere('a.id not in (
				select b.ac_id
				from ks_acl b
				where b.role_id = :role_id
			)')
			->setParameter('role_id', $role_id);
		
		$records = $qb->execute()->fetchAll();
		
		// DB portability
		$engine = DbAbs::getDbEngine($conn);
		$records = DbAbs::setCase($engine, $records);
		
		return $records;
	}
}