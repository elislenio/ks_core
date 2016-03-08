<?php
namespace Ks\CoreBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Ks\CoreBundle\Services\AC;

class UserEditType extends AbstractType
{
	private $ac;
	
	public function __construct(AC $ac)
    {
        $this->ac = $ac;
    }

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Ks\CoreBundle\Entity\User'
		));
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder
            ->add('username', FormType\TextType::class)
			->add('email')
			->add('first_name', FormType\TextType::class)
			->add('last_name', FormType\TextType::class)
			->add('picture', FormType\TextType::class)
			->add('locked', FormType\CheckboxType::class);
		
		if ($this->ac->localPasswordEnabled())
		{
			$builder
				->add('password_expired', FormType\CheckboxType::class);
		}
		
		$builder
			->add('save', FormType\SubmitType::class);
    }
}