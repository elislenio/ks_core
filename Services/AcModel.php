<?php
namespace Ks\CoreBundle\Services;

use Doctrine\ORM\EntityManager;
use Ks\CoreBundle\Form\Type\AcCreateType;
use Ks\CoreBundle\Form\Type\AcEditType;
use Ks\CoreBundle\Entity\AccessControl;

/**
 * AcPersist
 *
 */
class AcModel
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
		return $this->em->getRepository('KsCoreBundle:AccessControl')->find($id);
	}
	
	public function insert($ac)
	{
		$ac->normalizeId();
		$this->em->persist($ac);
		$this->em->flush();
	}
	
	public function update($ac)
	{
		$this->em->persist($ac);
		$this->em->flush();
	}
	
	public function delete($ac)
	{
		$this->em->remove($ac);
		$this->em->flush();
	}
	
	public function getFormCreate($ac)
	{
		return $this->form_factory->create(AcCreateType::class, $ac, array('validation_groups' => array('create')));
	}
	
	public function getFormEdit($ac)
	{
		return $this->form_factory->create(AcEditType::class, $ac, array('validation_groups' => array('update')));
	}
}
