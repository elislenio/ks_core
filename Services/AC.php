<?php
namespace Ks\CoreBundle\Services;

use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * AC
 *
 * Access control service
 */
class AC
{
	private $requestStack;
	private $pwd_management;
	
	public function __construct(RequestStack $requestStack, $pwd_management)
    {
		$this->requestStack = $requestStack;
		$this->pwd_management = $pwd_management;
    }
	
	public function isGranted($ac_name, $mask)
	{
		$session = $this->requestStack->getCurrentRequest()->getSession();
		$granted = $session->get('granted');
		$granted_mask = (int) $granted[$ac_name];
		return $mask & $granted_mask;
	}
	
	public function getGrants($ac_name)
	{
		$session = $this->requestStack->getCurrentRequest()->getSession();
		$granted = $session->get('granted');
		
		if (isset($granted[$ac_name]))
			$granted_mask = (int) $granted[$ac_name];
		else
			$granted_mask = 0;
		
		$grants = array();
		$grants['MASK_VIEW'] = MaskBuilder::MASK_VIEW & $granted_mask;
		$grants['MASK_CREATE'] = MaskBuilder::MASK_CREATE & $granted_mask;
		$grants['MASK_EDIT'] = MaskBuilder::MASK_EDIT & $granted_mask;
		$grants['MASK_DELETE'] = MaskBuilder::MASK_DELETE & $granted_mask;
		return $grants;
	}
	
	public function localPasswordEnabled()
	{
		if ($this->pwd_management == 'local')
			return true;
		
		return false;
	}
}
