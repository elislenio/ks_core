<?php
namespace Ks\CoreBundle\Services;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManager;
use Ks\CoreBundle\Services\AC;
use Ks\CoreBundle\Form\Type\UserCreateType;
use Ks\CoreBundle\Form\Type\UserEditType;
use Ks\CoreBundle\Form\Type\UserRoleCreateType;
use Ks\CoreBundle\Form\Type\UserPwdResetType;
use Ks\CoreBundle\Form\Type\UserPwdSelfChangeType;
use Ks\CoreBundle\Form\Type\UserPwdChangeType;
use Ks\CoreBundle\Entity\User;
use Ks\CoreBundle\Entity\UserRole;

/**
 * UserPersist
 *
 */
class UserModel
{
	private $em;
	private $form_factory;
	private $encoder;
	private $ac;
	
	public function __construct(EntityManager $em, $form_factory, AC $ac, UserPasswordEncoderInterface $encoder)
    {
		$this->em = $em;
		$this->form_factory = $form_factory;
		$this->ac = $ac;
		$this->encoder = $encoder;
    }
	
	public function get($id)
	{
		return $this->em->getRepository('KsCoreBundle:User')->find($id);
	}
	
	public function getByUsername($username)
	{
		return $this->em->getRepository('KsCoreBundle:User')->findOneBy(array('username' => $username));
	}
	
	public function getUserRole($id)
	{
		return $this->em->getRepository('KsCoreBundle:UserRole')->find($id);
	}
	
	public function insert($user)
	{
		$user->setPasswordExpired(true);
		$user->setFailureCount(0);
			
		if ($this->ac->localPasswordEnabled())
		{
			// Password encoding
			$encoded = $this->encoder->encodePassword($user, $user->getGeneratedPassword());
			$user->setPassword($encoded);
		}
		
		$this->em->persist($user);
		$this->em->flush();
	}
	
	public function update($user)
	{
		$this->em->persist($user);
		$this->em->flush();
	}
	
	public function delete($user)
	{
		$this->em->remove($user);
		$this->em->flush();
	}
	
	public function getFormCreate($user)
	{
		$validation_groups = array('create');
		
		if ($this->ac->localPasswordEnabled()) 
			$validation_groups[] = 'create_local';
		
		return $this->form_factory->create(UserCreateType::class, $user, array('validation_groups' => $validation_groups));
	}
	
	public function getFormEdit($user)
	{
		return $this->form_factory->create(UserEditType::class, $user, array('validation_groups' => array('update')));
	}
	
	public function resetPwd($user)
	{
		// Password encoding
		$encoded = $this->encoder->encodePassword($user, $user->getGeneratedPassword());
		$user->setPassword($encoded);
		$user->setPasswordExpired(true);
		
		// Unlock Account
		$user->unlockAccount();
		
		$this->em->persist($user);
		$this->em->flush();
	}
	
	public function getFormPwdReset($user)
	{
		return $this->form_factory->create(UserPwdResetType::class, $user, array('validation_groups' => array('pwdreset')));
	}
	
	public function setPasswordSelf($user, $password)
	{
		// Password encoding
		$encoded = $this->encoder->encodePassword($user, $password);
		$user->setPassword($encoded);
		$user->setPasswordExpired(false);
		$user->setGeneratedPassword('');
		
		$this->em->persist($user);
		$this->em->flush();
	}
	
	public function getFormPwdSelfChange()
	{
		return $this->form_factory->create(UserPwdSelfChangeType::class);
	}
	
	public function getFormPwdChange()
	{
		return $this->form_factory->create(UserPwdChangeType::class);
	}
	
	public function insertRole($user_role)
	{
		$role = $this->em->getRepository('KsCoreBundle:Role')->find($user_role->getRoleId());
		$user = $this->em->getRepository('KsCoreBundle:User')->find($user_role->getUserId());
		$user_role->setRole($role);
		$user_role->setUser($user);
		$this->em->persist($user_role);
		$this->em->flush();
	}
	
	public function deleteRole($user_role)
	{
		$this->em->remove($user_role);
		$this->em->flush();
	}
	
	public function getFormRoleAssign($user_role)
	{
		return $this->form_factory->create(UserRoleCreateType::class, $user_role, array('validation_groups' => array('create')));
	}
	
	public function registerLoginFailure($user)
	{
		$user->registerLoginFailure();
		$this->em->persist($user);
		$this->em->flush();
	}
	
	public function resetFailureCount($user)
	{
		$user->resetLoginFailure();
		$this->em->persist($user);
		$this->em->flush();
	}
	
	public function registerLoginSuccess($user)
	{
		$now = new \DateTime("now");
		$user->setLastLogin($now);
		$this->em->persist($user);
		$this->em->flush();
	}
	
	public function lockAccount($user)
	{
		$user->setLocked(true);
		$this->em->persist($user);
		$this->em->flush();
	}
	
	public function unlockAccount($user)
	{
		$user->unlockAccount();
		$this->em->persist($user);
		$this->em->flush();
	}
}
