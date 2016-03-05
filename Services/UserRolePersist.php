<?php
namespace Ks\CoreBundle\Services;

use Doctrine\ORM\EntityManager;
use Ks\CoreBundle\Form\Type\UserRoleCreateType;
use Ks\CoreBundle\Entity\UserRole;

/**
 * UserRole
 *
 */
class UserRolePersist
{
	private $em;
	private $form_factory;
	private $user_role;
	
	public function __construct(EntityManager $em, $form_factory)
    {
		$this->em = $em;
		$this->form_factory = $form_factory;
    }
	
	public function getFormCreate($user)
	{
		$this->user_role = new UserRole();
		$this->user_role->setUser($user);
		$this->user_role->setUserId($user->getId());
		return $this->form_factory->create(UserRoleCreateType::class, $this->user_role, array('validation_groups' => array('create')));
	}
	
	public function insert()
	{
		$role = $this->em->getRepository('KsCoreBundle:Role')->find($this->user_role->getRoleId());
		$this->user_role->setRole($role);
		$this->em->persist($this->user_role);
		$this->em->flush();
	}
	
	public function delete($user_role)
	{
		$this->em->remove($user_role);
		$this->em->flush();
	}
}
