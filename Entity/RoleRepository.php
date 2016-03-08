<?php
namespace Ks\CoreBundle\Entity;

/**
 * RoleRepository
 *
 */
class RoleRepository extends \Doctrine\ORM\EntityRepository
{
	public function getAvailableControlList($role_id)
    {
		$qb = $this->getEntityManager()->getConnection()->createQueryBuilder()
			->select('a.id, a.description')
			->from('ks_ac', 'a')
			->andWhere('a.id not in (
				select b.ac_id
				from ks_acl b
				where b.role_id = :role_id
			)')
			->setParameter('role_id', $role_id);
		
		$records = $qb->execute()->fetchAll();
		
		return $records;
	}
}