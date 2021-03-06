<?php

namespace App\Form\Type;

use App\Model\FilterUser;
use App\Security\Role;
use App\Translation\Locale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterUserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ['label' => 'form.admin.users.filter.username', 'required' => false])
            ->add('locale', ChoiceType::class, [
                'label' => 'form.admin.users.filter.locale',
                'choices' => [
                    Locale::TITLE_FR => Locale::FR,
                    Locale::TITLE_EN => Locale::EN,
                ],
                'required' => false,
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'form.admin.users.filter.role',
                'choices' => [
                    Role::TITLE_USER => Role::USER,
                    Role::TITLE_ADMIN => Role::ADMIN,
                    Role::TITLE_VALIDATOR => Role::VALIDATOR,
                ],
                'required' => false,
            ])
            ->add('enabled', ChoiceType::class, [
                'label' => 'form.admin.users.filter.enabled',
                'choices' => [
                    'answer.yes' => true,
                    'answer.no' => false,
                ],
                'required' => false,
            ])
            ->add('sort', ChoiceType::class, [
                'label' => 'form.admin.users.filter.sort',
                'choices' => [
                    'form.admin.users.filter.id_asc' => 'id|ASC',
                    'form.admin.users.filter.id_desc' => 'id|DESC',
                    'form.admin.users.filter.username_asc' => 'username|ASC',
                    'form.admin.users.filter.username_desc' => 'username|DESC',
                    'form.admin.users.filter.lastname_asc' => 'lastname|ASC',
                    'form.admin.users.filter.lastname_desc' => 'lastname|DESC',
                    'form.admin.users.filter.created_at_asc' => 'createdAt|ASC',
                    'form.admin.users.filter.created_at_desc' => 'createdAt|DESC',
                ],
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
            'data_class' => FilterUser::class,
        ]);
    }
}
