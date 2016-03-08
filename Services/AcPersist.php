<?php
namespace Ks\CoreBundle\Services;

use Doctrine\ORM\EntityManager;
use Ks\CoreBundle\Entity\AccessControl;

/**
 * AcPersist
 *
 */
class AcPersist
{
	private $em;
	
	public function __construct(EntityManager $em)
    {
		$this->em = $em;
    }
	
	public function insert($ac)
	{
		$ac->normalizeId();
		$this->em->persist($ac);
		$this->em->flush();
	}
	
	public function update($ac)
	{
		$this->em->persist($ac);
		$this->em->flush();
	}
	
	public function delete($ac)
	{
		$this->em->remove($ac);
		$this->em->flush();
	}
}
