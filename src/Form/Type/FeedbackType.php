<?php

namespace App\Form\Type;

use App\Entity\Feedback;
use App\Feedback\Subject;
use App\Form\DataTransformer\UserToUsernameTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackType extends AbstractType
{
    /** @var UserToUsernameTransformer */
    private $transformer;

    /**
     * @param UserToUsernameTransformer $transformer
     */
    public function __construct(UserToUsernameTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', TextType::class, ['label' => 'form.feedback.user'])
            ->add('subject', ChoiceType::class, [
                'label' => 'form.feedback.subject',
                'choices' => [
                    Subject::TITLE_INVALID_PICTURE => Subject::INVALID_PICTURE,
                    Subject::TITLE_OTHER => Subject::OTHER,
                ],
            ])
            ->add('comment', TextareaType::class, ['label' => 'form.feedback.comment'])
        ;

        $builder->get('user')->addModelTransformer($this->transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
        ]);
    }
}
