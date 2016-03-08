<?php
namespace Ks\CoreBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Doctrine\ORM\EntityManager;
use Ks\CoreBundle\Classes\RoleHelper;

class AclCreateType extends AbstractType
{
	private $em;
	
	public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$acl = $options['data'];
		
        $builder
			->add('role_id', FormType\HiddenType::class)
			->add('ac_id', FormType\ChoiceType::class, array('choices' => RoleHelper::getAvailableControlList($this->em, $acl->getRoleId()), 'choices_as_values' => true))
			->add('mask_view', FormType\CheckboxType::class)
			->add('mask_create', FormType\CheckboxType::class)
			->add('mask_edit', FormType\CheckboxType::class)
			->add('mask_delete', FormType\CheckboxType::class)
			->add('save', FormType\SubmitType::class)
        ;
    }

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Ks\CoreBundle\Entity\AccessControlList'
		));
	}
}