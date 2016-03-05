<?php
namespace Ks\CoreBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Firewall\ExceptionListener as BaseExceptionListener;

class ExceptionListener extends BaseExceptionListener
{
	/**
     * @param Request $request
     */
	// Experimental code: Sets target path to the referer path
    private function setRefererTargetPath(Request $request)
    {
		$referer = $request->headers->get('referer');
        $lastPath = parse_url($referer, PHP_URL_PATH);
        $request->getSession()->set('_security.main.target_path', $lastPath);
    }
	
    // Note that non-GET requests are already ignored
    protected function setTargetPath(Request $request)
    {
        // Do not save target path for XHR requests
        if ($request->isXmlHttpRequest()) 
		{
			$this->setRefererTargetPath($request);
			return;
		}
		
		// Do not save crud export URL
		if ($request->query->get('crud_action') && $request->query->get('crud_action') == 'export')
		{
			$this->setRefererTargetPath($request);
			return;
		}
		
		// You can add any more logic here as you want
		//...
		
        parent::setTargetPath($request);
    }
}