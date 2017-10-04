<?php

namespace App\Form\Type;

use App\Entity\User;
use App\Security\Role;
use App\Validator\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'form.admin.users.edit.username',
                'required' => false,
                'disabled' => true,
            ])
            ->add('firstname', TextType::class, ['label' => 'form.admin.users.edit.firstname'])
            ->add('lastname', TextType::class, ['label' => 'form.admin.users.edit.lastname'])
            ->add('email', EmailType::class, ['label' => 'form.admin.users.edit.email'])
            ->add('roles', ChoiceType::class, [
                'label' => 'form.admin.users.edit.roles',
                'choices' => [
                    Role::TITLE_USER => Role::USER,
                    Role::TITLE_ADMIN => Role::ADMIN,
                    Role::TITLE_VALIDATOR => Role::VALIDATOR,
                    Role::TITLE_CALLER => Role::CALLER,
                ],
                'required' => true,
                'multiple' => true,
            ])
            ->add('enabled', CheckboxType::class, ['label' => 'form.admin.users.edit.enabled', 'required' => false])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => [Group::USER_EDIT],
            'data_class' => User::class,
        ]);
    }
}
