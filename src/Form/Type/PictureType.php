<?php

namespace App\Form\Type;

use App\Entity\Picture;
use App\Form\DataTransformer\PlaceToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PictureType extends AbstractType
{
    /** @var PlaceToNumberTransformer */
    private $transformer;

    /**
     * @param PlaceToNumberTransformer $transformer
     */
    public function __construct(PlaceToNumberTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'form.travels.add_pictures.title'])
            ->add('date', DateType::class, [
                'label' => 'form.travels.add_pictures.date',
                'widget' => 'single_text',
            ])
            ->add('placeSearch', TextType::class, [
                'label' => 'form.travels.add_pictures.place_search',
                'mapped' => false,
            ])
            ->add('place', HiddenType::class)
            ->add('file', FileType::class, ['label' => 'form.travels.add_pictures.file'])
        ;

        $builder->get('place')->addModelTransformer($this->transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }
}
