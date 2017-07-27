<?php

namespace App\Form\Type;

use App\Entity\User;
use App\Translation\Locale;
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
                    'locale.fr' => Locale::FR,
                    'locale.en' => Locale::EN,
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => ['registration'],
            'data_class' => User::class,
        ]);
    }
}
