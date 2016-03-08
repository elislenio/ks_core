<?php
namespace Ks\CoreBundle\Classes;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Doctrine\ORM\EntityManager;

abstract class MenuHelper
{
	public static function getBranchList(EntityManager $em, $menu_id)
	{
		$records = $em->getRepository('KsCoreBundle:Menu')->getBranchList($menu_id);
		
		$options = array();
		$options['Seleccione un valor'] = '';
		
		foreach ($records as $r)
			$options[$r['label']] = $r['id'];
			
		return $options;
	}
	
	public static function getFunctionList(EntityManager $em)
	{
		$records = $em->getRepository('KsCoreBundle:AccessControl')->getFunctionList();
		
		$options = array();
		$options['Seleccione un valor'] = '';
		
		foreach ($records as $r)
			$options[$r['description']] = $r['id'];
			
		return $options;
	}
	
	public static function getMaskList()
	{
		$options = array();
		$options['Seleccione un valor'] = '';
		$options['Lectura'] = MaskBuilder::MASK_VIEW;
		$options['Alta'] = MaskBuilder::MASK_CREATE;
		$options['Modificación'] = MaskBuilder::MASK_EDIT;
		$options['Baja'] = MaskBuilder::MASK_DELETE;
			
		return $options;
	}
}