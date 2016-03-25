<?php
namespace Ks\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Ks\CoreBundle\Classes\DbAbs;

/**
 * ACLRepository
 *
 */
class ACLRepository extends EntityRepository
{
	public function getUserGrants($user_id)
    {
		$conn = $this->getEntityManager()->getConnection();
		
		$qb = $conn->createQueryBuilder()
			->select('a.ac_id', 'a.mask')
			->from('ks_acl', 'a')
			->innerJoin('a', 'ks_user_role', 'b', 'a.role_id = b.role_id')
			->where('b.user_id = :user_id')
			->setParameter('user_id', $user_id);
		
		$records = $qb->execute()->fetchAll();
		
		// DB portability
		$engine = DbAbs::getDbEngine($conn);
		$records = DbAbs::setCase($engine, $records);
		return $records;
	}
}
