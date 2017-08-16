<?php

namespace App\Form\Type;

use App\Entity\User;
use App\Security\Role;
use App\Translation\Locale;
use App\Validator\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, ['label' => 'form.registration.firstname'])
            ->add('lastname', TextType::class, ['label' => 'form.registration.lastname'])
            ->add('email', EmailType::class, ['label' => 'form.registration.email'])
            ->add('username', TextType::class, ['label' => 'form.registration.username'])
            ->add('locale', ChoiceType::class, [
                'label' => 'form.registration.locale',
                'choices' => [
                    Locale::TITLE_FR => Locale::FR,
                    Locale::TITLE_EN => Locale::EN,
                ],
            ])
        ;

        if (!$options['with_roles']) {
            return;
        }

        $builder->add('roles', ChoiceType::class, [
            'label' => 'form.admin.users.edit.roles',
            'choices' => [
                Role::TITLE_USER => Role::USER,
                Role::TITLE_ADMIN => Role::ADMIN,
                Role::TITLE_VALIDATOR => Role::VALIDATOR,
            ],
            'required' => true,
            'multiple' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => [Group::USER_REGISTRATION],
            'data_class' => User::class,
            'with_roles' => false,
        ]);
    }
}
