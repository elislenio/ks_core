<?php
namespace Ks\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Ks\CoreBundle\Classes\DbAbs;

/**
 * ACRepository
 *
 */
class ACRepository extends EntityRepository
{
	public function getFunctionList()
    {
		$conn = $this->getEntityManager()->getConnection();
		
		$qb = $conn->createQueryBuilder()
			->select('a.id, a.description')
			->from('ks_ac', 'a');
		
		$records = $qb->execute()->fetchAll();
		
		// DB portability
		$engine = DbAbs::getDbEngine($conn);
		$records = DbAbs::setCase($engine, $records);
		
		return $records;
	}
}
