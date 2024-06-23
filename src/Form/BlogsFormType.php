<?php

namespace App\Form;

use App\Entity\Blogs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', type: TextType::class)
            ->add('content', TextareaType::class, [
                'attr' => [
                    'rows' => 35
                ],
            ])
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'hours' => range(0, 23),
                'minutes' => range(0, 59),
                'seconds' => range(0, 59),
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Blogs::class,
        ]);
    }
}
