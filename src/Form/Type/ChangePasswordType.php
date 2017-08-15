<?php

namespace App\Form\Type;

use App\Entity\User;
use App\Validator\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', PasswordType::class, ['label' => 'form.change_password.current'])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'form.change_password.password_1'],
                'second_options' => ['label' => 'form.change_password.password_2'],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => [Group::USER_CHANGE_PASSWORD],
            'data_class' => User::class,
        ]);
    }
}
