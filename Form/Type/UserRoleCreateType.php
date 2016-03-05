<?php
namespace Ks\CoreBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Doctrine\ORM\EntityManager;

class UserRoleCreateType extends AbstractType
{
	private $em;
	private $user_role;
	
	public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Ks\CoreBundle\Entity\UserRole'
		));
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$this->user_role = $options['data'];
		
		$builder
			->add('user_id', FormType\HiddenType::class)
			->add('role_id', FormType\ChoiceType::class, array('label' => 'Rol', 'choices' => $this->getAvailableRolesList(), 'choices_as_values' => true))
			->add('save', FormType\SubmitType::class, array('label' => 'Guardar'));
    }
	
	private function getAvailableRolesList()
	{
		$records = $this->em->getRepository('KsCoreBundle:UserRole')->getAvailableRoles($this->user_role->getUserId());
		
		$options = array();
		$options['Seleccione un valor'] = '';
		
		foreach ($records as $r)
			$options[$r['description']] = $r['id'];
			
		return $options;
	}
}