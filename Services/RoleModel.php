<?php
namespace Ks\CoreBundle\Services;

use Doctrine\ORM\EntityManager;
use Ks\CoreBundle\Form\Type\RoleCreateType;
use Ks\CoreBundle\Form\Type\RoleEditType;
use Ks\CoreBundle\Form\Type\AclCreateType;
use Ks\CoreBundle\Form\Type\AclEditType;
use Ks\CoreBundle\Entity\Role;
use Ks\CoreBundle\Entity\AccessControl;
use Ks\CoreBundle\Entity\AccessControlList;

/**
 * RolePersist
 *
 */
class RoleModel
{
	private $em;
	private $form_factory;
	
	public function __construct(EntityManager $em, $form_factory)
    {
		$this->em = $em;
		$this->form_factory = $form_factory;
    }
	
	public function get($id)
	{
		return $this->em->getRepository('KsCoreBundle:Role')->find($id);
	}
	
	public function getACL($id)
	{
		return $this->em->getRepository('KsCoreBundle:AccessControlList')->find($id);
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
	
	public function insertAcl($role, $acl)
	{
		$acl->setRole($role);
		$ac = $this->em->getRepository('KsCoreBundle:AccessControl')->find($acl->getAcId());
		$acl->setAc($ac);
		$acl->buildMask();
		$this->em->persist($acl);
		$this->em->flush();
	}
	
	public function updateAcl($acl)
	{
		$acl->buildMask();
		$this->em->persist($acl);
		$this->em->flush();
	}
	
	public function deleteAcl($acl)
	{
		$this->em->remove($acl);
		$this->em->flush();
	}
	
	public function getFormCreate($role)
	{
		return $this->form_factory->create(RoleCreateType::class, $role, array('validation_groups' => array('create')));
	}
	
	public function getFormEdit($role)
	{
		return $this->form_factory->create(RoleEditType::class, $role, array('validation_groups' => array('update')));
	}
	
	public function getFormAclCreate($acl)
	{
		return $this->form_factory->create(AclCreateType::class, $acl, array('validation_groups' => array('create')));
	}
	
	public function getFormAclEdit($acl)
	{
		return $this->form_factory->create(AclEditType::class, $acl, array('validation_groups' => array('update')));
	}
}
