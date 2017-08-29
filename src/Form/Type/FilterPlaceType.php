<?php

namespace App\Form\Type;

use App\Model\FilterPlace;
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
            ->add('sort', ChoiceType::class, [
                'label' => 'form.admin.places.filter.sort',
                'choices' => [
                    'form.admin.places.filter.id' => 'id',
                    'form.admin.places.filter.title' => 'title',
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
