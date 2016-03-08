<?php
namespace Ks\CoreBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Doctrine\ORM\EntityManager;
use Ks\CoreBundle\Classes\MenuHelper;

class MenuItemEditType extends AbstractType
{
	private $em;
	
	public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$menu_item = $options['data'];
		
        $builder
			->add('parent_id', FormType\ChoiceType::class, array('choices' => MenuHelper::getBranchList($this->em, $menu_item->getMenuId()), 'choices_as_values' => true))
			->add('label', FormType\TextType::class)
			->add('route', FormType\TextType::class)
			->add('item_order', FormType\NumberType::class)
			->add('icon', FormType\TextType::class)
			->add('is_branch', FormType\CheckboxType::class)
			->add('visible', FormType\CheckboxType::class)
			->add('ac_id', FormType\ChoiceType::class, array('choices' => MenuHelper::getFunctionList($this->em), 'choices_as_values' => true))
			->add('mask', FormType\ChoiceType::class, array('choices' => MenuHelper::getMaskList(), 'choices_as_values' => true))
			->add('save', FormType\SubmitType::class)
        ;
    }

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Ks\CoreBundle\Entity\MenuItem'
		));
	}
}