<?php
namespace Ks\CoreBundle\Classes;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;

/**
 * AuthenticationListener
 *
 */
class AuthenticationListener
{
	private $token_storage;
	private $em;
	private $requestStack;
	
	public function __construct(TokenStorage $token_storage, EntityManager $em, RequestStack $requestStack)
    {
		$this->token_storage = $token_storage;
		$this->em = $em;
        $this->requestStack = $requestStack;
    }
	
	/**
	 * Store grants into session for performance
	 */
	public function saveToSession()
	{
		$user = $this->token_storage->getToken()->getUser();
		$acls = $this->em->getRepository('KsCoreBundle:AccessControlList')->getUserGrants($user->getId());
		$granted = array();
		
		// Cumulative permissions
		foreach($acls as $acl)
		{
			$ac_id = $acl['ac_id'];
			$mask = $acl['mask'];
			
			if (isset($granted[$ac_id]))
				$granted[$ac_id] = $granted[$ac_id]|$mask;
			else
				$granted[$ac_id] = $mask;
		}
		
		// Save on session
		$session = $this->requestStack->getCurrentRequest()->getSession();
		$session->set('granted', $granted);
	}
	
	/**
	 * onAuthenticationSuccess
	 */
	public function onAuthenticationSuccess( InteractiveLoginEvent $event )
    {
		$this->saveToSession();
    }
	
	/**
	 * onAuthenticationFailure
	 */
	public function onAuthenticationFailure( AuthenticationFailureEvent $event )
	{
		
	}
}
