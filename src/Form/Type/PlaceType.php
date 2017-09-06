<?php

namespace App\Form\Type;

use App\Entity\Place;
use App\Translation\Locale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'form.admin.places.create_edit.title'])
            ->add('locale', ChoiceType::class, [
                'label' => 'form.admin.places.create_edit.locale',
                'choices' => [
                    Locale::TITLE_FR => Locale::FR,
                    Locale::TITLE_EN => Locale::EN,
                ],
            ])
            ->add('latitude', HiddenType::class)
            ->add('longitude', HiddenType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
