<?php
namespace Ks\CoreBundle\Classes;

use Doctrine\ORM\EntityManager;

abstract class RoleHelper
{
	public static function getAvailableControlList(EntityManager $em, $role_id)
	{
		$records = $em->getRepository('KsCoreBundle:Role')->getAvailableControlList($role_id);
		
		$options = array();
		$options['Seleccione un valor'] = '';
		
		foreach ($records as $r)
			$options[$r['description']] = $r['id'];
			
		return $options;
	}
}