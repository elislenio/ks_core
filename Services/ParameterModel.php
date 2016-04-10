<?php
namespace Ks\CoreBundle\Services;

use Doctrine\ORM\EntityManager;
use Ks\CoreBundle\Form\Type\ParamCreateType;
use Ks\CoreBundle\Form\Type\ParamEditType;
use Ks\CoreBundle\Entity\Parameter;

/**
 * ParameterModel
 *
 */
class ParameterModel
{
	private $em;
	private $form_factory;
	
	public function __construct(EntityManager $em, $form_factory)
    {
		$this->em = $em;
		$this->form_factory = $form_factory;
    }
	
	public function get($id)
	{
		return $this->em->getRepository('KsCoreBundle:Parameter')->find($id);
	}
	
	public function getValue($id)
	{
		$parameter = $this->em->getRepository('KsCoreBundle:Parameter')->find($id);
		return $parameter->getValue();
	}
	
	public function insert($param)
	{
		$param->normalizeId();
		$this->em->persist($param);
		$this->em->flush();
	}
	
	public function update($param)
	{
		$this->em->persist($param);
		$this->em->flush();
	}
	
	public function delete($param)
	{
		$this->em->remove($param);
		$this->em->flush();
	}
	
	public function getFormCreate($param)
	{
		return $this->form_factory->create(ParamCreateType::class, $param, array('validation_groups' => array('create')));
	}
	
	public function getFormEdit($param)
	{
		return $this->form_factory->create(ParamEditType::class, $param, array('validation_groups' => array('update')));
	}
}
