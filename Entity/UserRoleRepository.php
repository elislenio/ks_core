<?php
namespace Ks\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserRoleRepository
 *
 */
class UserRoleRepository extends EntityRepository
{
	public function getAvailableRoles($user_id)
    {
		$qb = $this->getEntityManager()->getConnection()->createQueryBuilder()
			->select('a.id, a.description')
			->from('ks_role', 'a')
			->andWhere('a.id not in (
				select b.role_id
				from ks_user_role b
				where b.user_id = :user_id
				)')
			->setParameter('user_id', $user_id);
		
		$records = $qb->execute()->fetchAll();
		
		return $records;
	}
}
