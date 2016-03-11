<?php
namespace Ks\CoreBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Ldap\LdapClient;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Ks\CoreBundle\Security\UserChecker;

class KsAuthenticator implements SimpleFormAuthenticatorInterface
{
    private $userChecker;
	private $encoder;
	private $pwd_management;
	private $ldap;
	private $dnString;

    public function __construct(UserChecker $userChecker, UserPasswordEncoderInterface $encoder, $pwd_management, LdapClient $ldap = null, $dnString)
    {
        $this->userChecker = $userChecker;
		$this->encoder = $encoder;
		$this->pwd_management = $pwd_management;
		$this->ldap = $ldap;
		$this->dnString = $dnString;
    }

	public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
		try {
            $user = $userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $e) {
			throw new CustomUserMessageAuthenticationException('Invalid credentials.');
        }
		
		$this->userChecker->setUser($user);
		$username = $token->getUsername();
		$password = $token->getCredentials();
		
		if ($this->pwd_management == 'ldap')
		{
			$this->userChecker->checkPreAuth($user);
		
			$username = $this->ldap->escape($username, '', LDAP_ESCAPE_DN);
			$dn = str_replace('{username}', $username, $this->dnString);
		
			try {
				
				$this->ldap->bind($dn, $password);
				
				$this->userChecker->resetFailureCount();
				// do not check for credentials expired since this is done in the LDAP server
				$this->userChecker->setPostAuthRules(array());
				$this->userChecker->checkPostAuth($user);
				
			} catch (ConnectionException $e) {
				
				$cause = $e->getMessage();
				
				if ($cause == 'Invalid credentials')
				{
					$this->userChecker->registerLoginAttempt();
					throw new CustomUserMessageAuthenticationException('Invalid credentials.');
				}
				else
				{
					throw new CustomUserMessageAuthenticationException($cause);
				}
			}
		}
		else
		{
			$this->userChecker->checkPreAuth($user);
			
			if (! $this->encoder->isPasswordValid($user, $password))
			{
				$this->userChecker->registerLoginAttempt();
				
				if ($this->userChecker->isLocked())
					throw new CustomUserMessageAuthenticationException('Account is locked.');
				else
					throw new CustomUserMessageAuthenticationException('Invalid credentials.');
			}
			
			$this->userChecker->resetFailureCount();
			$this->userChecker->checkPostAuth($user);
		}
		
		$user_token = new UsernamePasswordToken(
			$user,
			$password,
			$providerKey,
			$user->getRoles()
		);
		
		$this->userChecker->registerLoginSuccess();
		
		return $user_token;
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken
            && $token->getProviderKey() === $providerKey;
    }

    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }
}