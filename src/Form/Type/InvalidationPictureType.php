<?php

namespace App\Form\Type;

use App\Checker\InvalidatePictureReason;
use App\Entity\InvalidationPicture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvalidationPictureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', TextType::class, [
                'label' => 'form.validation.pictures.invalidation.user',
                'disabled' => true,
                'required' => false,
            ])
            ->add('travel', TextType::class, [
                'label' => 'form.validation.pictures.invalidation.travel',
                'disabled' => true,
                'required' => false,
            ])
            ->add('place', TextType::class, [
                'label' => 'form.validation.pictures.invalidation.place',
                'disabled' => true,
                'required' => false,
            ])
            ->add('reason', ChoiceType::class, [
                'label' => 'form.validation.pictures.invalidation.reason',
                'choices' => [
                    InvalidatePictureReason::TITLE_INVALID => InvalidatePictureReason::INVALID,
                    InvalidatePictureReason::TITLE_PORN => InvalidatePictureReason::PORN,
                    InvalidatePictureReason::TITLE_PEDOPHILIA => InvalidatePictureReason::PEDOPHILIA,
                    InvalidatePictureReason::TITLE_OTHERS => InvalidatePictureReason::OTHERS,
                ],
                'required' => true,
            ])
            ->add('comment', TextType::class, [
                'label' => 'form.validation.pictures.invalidation.comment',
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
            'data_class' => InvalidationPicture::class,
        ]);
    }
}
