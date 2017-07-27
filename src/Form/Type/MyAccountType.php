<?php

namespace App\Form\Type;

use App\Entity\User;
use App\Translation\Locale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MyAccountType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, ['label' => 'form.my_account.firstname'])
            ->add('lastname', TextType::class, ['label' => 'form.my_account.lastname'])
            ->add('email', EmailType::class, ['label' => 'form.my_account.email'])
            ->add('locale', ChoiceType::class, [
                'label' => 'form.my_account.locale',
                'choices' => [
                    'locale.fr' => Locale::FR,
                    'locale.en' => Locale::EN,
                ],
            ])
            ->add('currentPassword', PasswordType::class, [
                'label' => 'form.my_account.current_password',
                'required' => false,
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'form.my_account.new_password_1'],
                'second_options' => ['label' => 'form.my_account.new_password_2'],
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => ['my_account'],
            'data_class' => User::class,
        ]);
    }
}
