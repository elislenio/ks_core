<?php

namespace Ks\CoreBundle\Classes;

use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * AuthenticationListener
 *
 */
class AuthenticationListener
{
	private $token_storage;
	private $conn;
	private $requestStack;
	
	public function __construct(TokenStorage $token_storage, Connection $conn, RequestStack $requestStack)
    {
		$this->token_storage = $token_storage;
        $this->conn = $conn;
		$this->requestStack = $requestStack;
    }
	
	/**
	 * Store grants into session for performance
	 */
	public function saveToSession()
	{
		$user = $this->token_storage->getToken()->getUser();
		
		$qb = $this->conn->createQueryBuilder();
		
		$qb
			->select('a.ac_id', 'a.mask')
			->from('ks_acl', 'a')
			->innerJoin('a', 'ks_user_role', 'b', 'a.role_id = b.role_id')
			->where('b.user_id = ?')
			->setParameter(0, $user->getId())
		;
		
		$grants = $qb->execute()->fetchAll();
		$granted = array();
		
		// Actual permissions
		foreach($grants as $a)
		{
			if (isset($granted[$a['ac_id']]))
				$granted[$a['ac_id']] = $granted[$a['ac_id']]|$a['mask'];
			else
				$granted[$a['ac_id']] = $a['mask'];
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
