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
			throw new CustomUserMessageAuthenticationException('Invalid username or password');
        }
		
		$username = $token->getUsername();
		$password = $token->getCredentials();
		
		if ($this->pwd_management == 'ldap')
		{
			$this->userChecker->checkPreAuth($user);
		
			$username = $this->ldap->escape($username, '', LDAP_ESCAPE_DN);
			$dn = str_replace('{username}', $username, $this->dnString);
		
			try {
				
				$this->ldap->bind($dn, $password);
				
				// do not check for credentials expired since this is done in the LDAP server
				$rules = array('locked', 'account_expired');
				$this->userChecker->setPostAuthRules($rules);
				$this->userChecker->checkPostAuth($user);
				
			} catch (ConnectionException $e) {
				throw new CustomUserMessageAuthenticationException($e->getMessage());
			}
		}
		else
		{
			$this->userChecker->checkPreAuth($user);
			
			if (! $this->encoder->isPasswordValid($user, $password))
				throw new CustomUserMessageAuthenticationException('Invalid username or password');
				
			$this->userChecker->checkPostAuth($user);
		}
		
		$user_token = new UsernamePasswordToken(
			$user,
			$password,
			$providerKey,
			$user->getRoles()
		);
		
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
