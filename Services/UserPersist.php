<?php
namespace Ks\CoreBundle\Services;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManager;
use Ks\CoreBundle\Services\AC;
use Ks\CoreBundle\Form\Type\UserCreateType;
use Ks\CoreBundle\Form\Type\UserEditType;
use Ks\CoreBundle\Form\Type\UserPwdResetType;
use Ks\CoreBundle\Entity\User;

/**
 * UserPersist
 *
 */
class UserPersist
{
	private $em;
	private $form_factory;
	private $encoder;
	private $ac;
	private $user;
	
	public function __construct(EntityManager $em, $form_factory, AC $ac, UserPasswordEncoderInterface $encoder)
    {
		$this->em = $em;
		$this->form_factory = $form_factory;
		$this->ac = $ac;
		$this->encoder = $encoder;
    }
	
	public function getFormCreate()
	{
		$this->user = new User();
		
		$validation_groups = array('create');
		
		if ($this->ac->localPasswordEnabled()) 
			$validation_groups[] = 'create_local';
		
		return $this->form_factory->create(UserCreateType::class, $this->user, array('validation_groups' => $validation_groups));
	}
	
	public function getFormEdit($user)
	{
		$this->user = $user;
		return $this->form_factory->create(UserEditType::class, $this->user, array('validation_groups' => array('update')));
	}
	
	public function getFormPwdReset($user)
	{
		$this->user = $user;
		return $this->form_factory->create(UserPwdResetType::class, $this->user, array('validation_groups' => array('pwdreset')));
	}
	
	public function insert()
	{
		$this->user->setPasswordExpired(true);
			
		if ($this->ac->localPasswordEnabled())
		{
			// Password encoding
			$encoded = $this->encoder->encodePassword($this->user, $this->user->getGeneratedPassword());
			$this->user->setPassword($encoded);
		}
		
		$this->em->persist($this->user);
		$this->em->flush();
	}
	
	public function update()
	{
		$this->em->persist($this->user);
		$this->em->flush();
	}
	
	public function delete($user)
	{
		$this->em->remove($user);
		$this->em->flush();
	}
	
	public function resetPwd()
	{
		// Password encoding
		$encoded = $this->encoder->encodePassword($this->user, $this->user->getGeneratedPassword());
		$this->user->setPassword($encoded);
		$this->user->setPasswordExpired(true);
		
		$this->em->persist($this->user);
		$this->em->flush();
	}
}
