<?php
namespace Ks\CoreBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{
	protected $grants;
	protected $pdo_exception;
	
	protected function getAcGrants($ac)
    {
		$this->grants = $this->get('ks.core.ac')->getGrants($ac);
    }
	
	protected function granted($mask)
	{
		if (! isset($this->grants[$mask]))	return false;
		return $this->grants[$mask];
	}
	
	protected function translateError($code)
	{
		$msg = "";
		
		switch ($code)
		{
			case '23000':
				$msg = 'El registro que intenta crear ya existe.';
				break;
			default:
				$msg = 'Se ha producido un error.';
				break;
		}
		
		return $msg;
	}
	
	protected function handleException($e)
	{
		$this->get('logger')->error($e->getMessage());
		$this->pdo_exception = $e->getPrevious();
		return $this->translateError($this->pdo_exception->getCode());
	}
}