<?php
namespace Ks\CoreBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as FormType;

class UserPwdResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('generated_password', FormType\TextType::class, array('label' => 'Nueva contraseña', 'attr' => array('readonly' => 'readonly')))
			->add('gen_pwd', FormType\ButtonType::class, array('label' => 'Generar', 'attr' => array('onclick' => 'genPwd()')))
			->add('save', FormType\SubmitType::class, array('label' => 'Guardar'))
        ;
    }

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Ks\CoreBundle\Entity\User'
		));
	}
}