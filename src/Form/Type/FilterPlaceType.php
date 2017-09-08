<?php

namespace App\Form\Type;

use App\Model\FilterPlace;
use App\Translation\Locale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterPlaceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'form.admin.places.filter.title', 'required' => false])
            ->add('locale', ChoiceType::class, [
                'label' => 'form.admin.places.filter.locale',
                'choices' => [
                    Locale::TITLE_FR => Locale::FR,
                    Locale::TITLE_EN => Locale::EN,
                ],
                'required' => false,
            ])
            ->add('sort', ChoiceType::class, [
                'label' => 'form.admin.places.filter.sort',
                'choices' => [
                    'form.admin.places.filter.id_asc' => 'id|ASC',
                    'form.admin.places.filter.id_desc' => 'id|DESC',
                    'form.admin.places.filter.title_asc' => 'title|ASC',
                    'form.admin.places.filter.title_desc' => 'title|DESC',
                    'form.admin.places.filter.created_at_asc' => 'createdAt|ASC',
                    'form.admin.places.filter.created_at_desc' => 'createdAt|DESC',
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
            'data_class' => FilterPlace::class,
        ]);
    }
}
