<?php

namespace App\Form\Type;

use App\Feedback\Subject;
use App\Model\FilterFeedback;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterFeedbackType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ['label' => 'form.call.feedback.filter.username', 'required' => false])
            ->add('subject', ChoiceType::class, [
                'label' => 'form.call.feedback.filter.subject',
                'choices' => [
                    Subject::TITLE_INVALID_PICTURE => Subject::INVALID_PICTURE,
                    Subject::TITLE_OTHER => Subject::OTHER,
                ],
                'required' => false,
            ])
            ->add('processed', ChoiceType::class, [
                'label' => 'form.call.feedback.filter.processed',
                'choices' => [
                    'answer.yes' => true,
                    'answer.no' => false,
                ],
                'required' => false,
            ])
            ->add('sort', ChoiceType::class, [
                'label' => 'form.call.feedback.filter.sort',
                'choices' => [
                    'form.call.feedback.filter.id_asc' => 'id|ASC',
                    'form.call.feedback.filter.id_desc' => 'id|DESC',
                    'form.call.feedback.filter.created_at_asc' => 'createdAt|ASC',
                    'form.call.feedback.filter.created_at_desc' => 'createdAt|DESC',
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
            'data_class' => FilterFeedback::class,
        ]);
    }
}
