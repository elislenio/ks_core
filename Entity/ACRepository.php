<?php
namespace Ks\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ACRepository
 *
 */
class ACRepository extends EntityRepository
{
	public function getFunctionList()
    {
		$qb = $this->getEntityManager()->getConnection()->createQueryBuilder()
			->select('a.id, a.description')
			->from('ks_ac', 'a');
		
		$records = $qb->execute()->fetchAll();
		
		return $records;
	}
}
