<?php
namespace Ks\CoreBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Ks\CoreBundle\Services\AC;

class UserCreateType extends AbstractType
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
            ->add('username', FormType\TextType::class, array('label' => 'Nombre de usuario'))
			->add('email')
			->add('first_name', FormType\TextType::class, array('label' => 'Nombre'))
			->add('last_name', FormType\TextType::class, array('label' => 'Apellido'))
			->add('picture', FormType\TextType::class, array('label' => 'Imagen'));
		
		if ($this->ac->localPasswordEnabled())
		{
			$builder
				->add('generated_password', FormType\TextType::class, array('label' => 'Contraseña', 'attr' => array('readonly' => 'readonly')))
				->add('gen_pwd', FormType\ButtonType::class, array('label' => 'Generar', 'attr' => array('onclick' => 'genPwd()')));
		}
		
		$builder
			->add('save', FormType\SubmitType::class, array('label' => 'Guardar'));
    }
}