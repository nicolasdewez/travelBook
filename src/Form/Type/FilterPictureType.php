<?php

namespace App\Form\Type;

use App\Model\FilterPicture;
use App\Workflow\CheckPictureDefinitionWorkflow as Definition;
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
        $builder->add('title', TextType::class, [
            'label' => 'form.validation.pictures.filter.title',
            'required' => false,
        ]);

        if ($options['username']) {
            $builder->add('username', TextType::class, [
                'label' => 'form.validation.pictures.filter.username',
                'required' => false,
            ]);
        }

        if ($options['state_processed']) {
            $builder->add('state', ChoiceType::class, [
                'label' => 'form.validation.pictures.filter.check_state',
                'choices' => [
                    Definition::PLACE_TITLE_VALIDATED => Definition::PLACE_VALIDATED,
                    Definition::PLACE_TITLE_INVALID => Definition::PLACE_INVALID,
                ],
                'required' => false,
            ]);
        }

        $builder->add('sort', ChoiceType::class, [
                'label' => 'form.validation.pictures.filter.sort',
                'choices' => [
                    'form.validation.pictures.filter.id' => 'id',
                    'form.validation.pictures.filter.date' => 'date',
                    'form.validation.pictures.filter.title' => 'title',
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
            'state_processed' => false,
            'username' => false,
        ]);
    }
}
