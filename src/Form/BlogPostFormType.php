<?php

namespace App\Form;

use App\Entity\Blogs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class BlogPostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a title',
                    ]),
                    new Length([
                        'max' => 75,
                        'maxMessage' => 'Title cannot be longer than {{ limit }} characters',
                    ]),
                ]
            ])
            ->add('content', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a content',
                    ]),
                    new Length([
                        'max' => 300,
                        'maxMessage' => 'Content cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('date', null, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a date',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Blogs::class,
        ]);
    }
}
