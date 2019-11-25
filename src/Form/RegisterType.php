<?php


namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Username',]
            ])
            ->add('surname', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Surname',]
            ])
            ->add('user_nick', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'User nickname',]
            ])
            ->add('email' , EmailType::class,[
                'label' => false,
                'attr' => ['placeholder' => 'Email',]])
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array(
                    'label' => false,
                    'attr' => ['placeholder' => 'Password']),
                'second_options' => array(
                    'label' => false,
                    'attr' => ['placeholder' => 'Repeat password',]),
                'invalid_message' => 'Please make sure your passwords match',
            ))
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => array('username_validation'),
        ]);
    }
}