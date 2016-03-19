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
			case 1062:
				$msg = 'El registro que intenta crear ya existe.';
				break;
			default:
				$msg = 'Se ha producido un error.';
				break;
		}
		
		return $msg;
	}
	
	protected function getPDOException($e)
	{
		if ($e instanceof PDOException)
			return $e;
		
		$prev = $e->getPrevious();
		
		if (! $prev)
			return $e;
		
		return $this->getPDOException($prev);
	}
	
	protected function handleException($e)
	{
		$this->pdo_exception = $this->getPDOException($e);
		$err = $this->pdo_exception->errorInfo;
		$code = $err[1];
		$msg = $err[2];
		$this->get('logger')->error($msg);
		return $this->translateError($code);
	}
}