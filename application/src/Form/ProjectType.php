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
                'label'      => 'app.project.project_title',
                'label_attr' => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label'      => 'app.project.description',
                'label_attr' => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
            ])
            ->add('startDate', null, [
                'label'      => 'app.project.start_date',
                'widget'     => 'single_text',
                'label_attr' => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
            ])
            ->add('endDate', null, [
                'label'      => 'app.project.end_date',
                'widget'     => 'single_text',
                'label_attr' => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
            ])
            ->add('customer', EntityType::class, [
                'label'        => 'app.project.customer',
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
