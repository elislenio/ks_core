<?php
namespace Ks\CoreBundle\Services;

use Doctrine\ORM\EntityManager;
use Ks\CoreBundle\Entity\Role;
use Ks\CoreBundle\Entity\AccessControl;
use Ks\CoreBundle\Entity\AccessControlList;

/**
 * RolePersist
 *
 */
class RolePersist
{
	private $em;
	
	public function __construct(EntityManager $em)
    {
		$this->em = $em;
    }
	
	public function insert($role)
	{
		$role->normalizeName();
		$this->em->persist($role);
		$this->em->flush();
	}
	
	public function update($role)
	{
		$role->normalizeName();
		$this->em->persist($role);
		$this->em->flush();
	}
	
	public function delete($role)
	{
		$this->em->remove($role);
		$this->em->flush();
	}
	
	public function insertPermission($role, $acl)
	{
		$acl->setRole($role);
		$ac = $this->em->getRepository('KsCoreBundle:AccessControl')->find($acl->getAcId());
		$acl->setAc($ac);
		$acl->buildMask();
		$this->em->persist($acl);
		$this->em->flush();
	}
	
	public function updatePermission($acl)
	{
		$acl->buildMask();
		$this->em->persist($acl);
		$this->em->flush();
	}
	
	public function deletePermission($acl)
	{
		$this->em->remove($acl);
		$this->em->flush();
	}
}
