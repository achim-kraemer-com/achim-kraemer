<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Project;
use App\Entity\TimeEntry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimeEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', null, [
                'label'      => 'app.time_entry.date',
                'widget'     => 'single_text',
                'label_attr' => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
            ])
            ->add('hours', NumberType::class, [
                'required'   => false,
                'label'      => 'app.time_entry.hours',
                'label_attr' => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
            ])
            ->add('price', NumberType::class, [
                'required'   => false,
                'label'      => 'app.time_entry.price',
                'label_attr' => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label'      => 'app.time_entry.description',
                'label_attr' => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
            ])
            ->add('status', ChoiceType::class, [
                'label'      => 'app.time_entry.invoiced',
                'choices'    => TimeEntry::STATUS_TYPES,
                'label_attr' => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
            ])
            ->add('project', EntityType::class, [
                'label'        => 'app.time_entry.project',
                'class'        => Project::class,
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
            'data_class' => TimeEntry::class,
        ]);
    }
}
