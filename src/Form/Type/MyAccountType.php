<?php

namespace App\Form\Type;

use App\Entity\User;
use App\Translation\Locale;
use App\Validator\Group;
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
                    Locale::TITLE_FR => Locale::FR,
                    Locale::TITLE_EN => Locale::EN,
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
            'validation_groups' => [Group::USER_MY_ACCOUNT],
            'data_class' => User::class,
        ]);
    }
}
