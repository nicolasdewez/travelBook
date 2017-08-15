<?php

namespace App\Form\Type;

use App\Entity\User;
use App\Validator\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordLostType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ['label' => 'form.password_lost.username'])
            ->add('email', EmailType::class, ['label' => 'form.password_lost.email'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => [Group::USER_PASSWORD_LOST],
            'data_class' => User::class,
        ]);
    }
}
