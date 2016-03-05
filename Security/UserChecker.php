<?php
namespace Ks\CoreBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;

class UserChecker implements UserCheckerInterface
{
	private $user;
	private $preAuthRules;
	private $postAuthRules;
	
	public function __construct()
    {
        $this->preAuthRules = array('enabled');
		$this->postAuthRules = array('locked', 'account_expired', 'credentials_expired');
    }
	
	public function setPreAuthRules($rules)
	{
		$this->preAuthRules = $rules;
		return $this;
	}
	
	public function getPreAuthRules()
	{
		return $this->preAuthRules;
	}
	
	public function setPostAuthRules($rules)
	{
		$this->postAuthRules = $rules;
		return $this;
	}
	
	public function getPostAuthRules()
	{
		return $this->postAuthRules;
	}
	
	private function executeRule($rule)
	{
		switch ($rule)
		{
			case 'enabled':
				$this->checkEnabled();
				break;
			case 'locked':
				$this->checkLocked();
				break;
			case 'account_expired':
				$this->checkAccountExpired();
				break;
			case 'credentials_expired':
				$this->checkCredentialsExpired();
				break;
		}
	}
	
	private function checkEnabled()
	{
		if (! $this->user->isEnabled()) {
            $ex = new DisabledException('User account is disabled.');
            $ex->setUser($this->user);
            throw $ex;
        }
	}
	
	private function checkLocked()
	{
		if (! $this->user->isAccountNonLocked()) {
            $ex = new LockedException('User account is locked.');
            $ex->setUser($this->user);
            throw $ex;
        }
	}
    
	private function checkAccountExpired()
	{
		if (! $this->user->isAccountNonExpired()) {
			$ex = new AccountExpiredException('User account has expired.');
            $ex->setUser($this->user);
            throw $ex;
        }
	}
	
	private function checkCredentialsExpired()
	{
		 if (!$this->user->isCredentialsNonExpired()) {
            $ex = new CredentialsExpiredException('User credentials have expired.');
            $ex->setUser($this->user);
            throw $ex;
        }
	}
	
	public function checkPreAuth(UserInterface $user)
    {
		//die('checkPreAuth');
        if (!$user instanceof AdvancedUserInterface) {
            return;
        }
		
		$this->user = $user;
		
        foreach($this->preAuthRules as $rule)
			$this->executeRule($rule);
			
    }

    public function checkPostAuth(UserInterface $user)
    {
		//die('checkPostAuth');
        if (!$user instanceof AdvancedUserInterface) {
            return;
        }
		
		$this->user = $user;
		
        foreach($this->postAuthRules as $rule)
			$this->executeRule($rule);
		
    }
}