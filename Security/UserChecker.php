<?php
namespace Ks\CoreBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Ks\CoreBundle\Services\UserPersist;

class UserChecker implements UserCheckerInterface
{
	private $user;
	private $preAuthRules;
	private $postAuthRules;
	private $user_model;
	private $login_security;
	private $login_security_threshold;
	
	public function __construct(UserPersist $user_model, $login_security, $login_security_threshold)
    {
		$this->user_model = $user_model;
		$this->login_security = $login_security;
		$this->login_security_threshold = $login_security_threshold;
        $this->preAuthRules = array('locked', 'enabled', 'account_expired');
		$this->postAuthRules = array('credentials_expired');
    }
	
	public function setUser(UserInterface $user)
	{
		if (!$user instanceof AdvancedUserInterface) {
            return;
        }
		
		$this->user = $user;
		return $this;
	}
	
	public function getUserModel()
	{
		return $this->user_model;
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
		foreach($this->preAuthRules as $rule)
			$this->executeRule($rule);
			
    }

    public function checkPostAuth(UserInterface $user)
    {
		foreach($this->postAuthRules as $rule)
			$this->executeRule($rule);
    }
	
	public function registerLoginAttempt()
	{
		$this->user_model->registerLoginFailure($this->user);
		
		if ($this->login_security == 'none')
			return;
		
		// Activate security if threshold is reached
		if ($this->user->getFailureCount() >= $this->login_security_threshold)
		{
			if ($this->login_security == 'lock')
				$this->user_model->lockAccount($this->user);
		}
	}
	
	public function resetFailureCount()
	{
		$this->user_model->resetFailureCount($this->user);
	}
	
	public function registerLoginSuccess()
	{
		$this->user_model->registerLoginSuccess($this->user);
	}
	
	public function isLocked()
	{
		return $this->user->getLocked();
	}
}