<?php

namespace App\Form\Type;

use App\Model\FilterPicture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterPictureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'form.validation.pictures.list.title', 'required' => false])
            ->add('sort', ChoiceType::class, [
                'label' => 'form.validation.pictures.list.sort',
                'choices' => [
                    'form.validation.pictures.list.id' => 'id',
                    'form.validation.pictures.list.date' => 'date',
                    'form.validation.pictures.list.title' => 'title',
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
            'data_class' => FilterPicture::class,
        ]);
    }
}
