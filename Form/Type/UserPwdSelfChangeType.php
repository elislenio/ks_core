<?php
namespace Ks\CoreBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class UserPwdSelfChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('old_password', FormType\PasswordType::class, array(
				'constraints' => array(
					new NotBlank(),
					new UserPassword()
				)
			))
			->add('new_password', FormType\RepeatedType::class, array(
				'type' => FormType\PasswordType::class,
				'invalid_message' => 'Las contraseñas no coinciden',
				'options' => array('attr' => array('class' => 'password-field', 'required' => true)),
				'first_options'  => array('label' => 'Nueva contraseña'),
				'second_options' => array('label' => 'Repita la nueva contraseña'),
				'constraints' => new NotBlank()
			))
			->add('save', FormType\SubmitType::class)
        ;
    }
}