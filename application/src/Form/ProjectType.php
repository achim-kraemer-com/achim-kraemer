<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label'      => 'title',
                'label_attr' => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label'      => 'description',
                'label_attr' => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
            ])
            ->add('startDate', null, [
                'widget'     => 'single_text',
                'label_attr' => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
            ])
            ->add('endDate', null, [
                'widget'     => 'single_text',
                'label_attr' => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
            ])
            ->add('customer', EntityType::class, [
                'class'        => Customer::class,
                'label_attr'   => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
