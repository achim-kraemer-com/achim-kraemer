<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\TimeEntry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('invoice_date')
            ->add('time_entries', EntityType::class, [
                'class'        => TimeEntry::class,
                'choices'      => $options['time_entries'], // Nur offene Arbeitsstunden
                'choice_label' => static fn (TimeEntry $timeEntry) => \sprintf('%s - %s hours', $timeEntry->getDate()->format('Y-m-d'), $timeEntry->getHours()),
                'multiple'     => true,
                'expanded'     => true, // Checkbox für jede Stunde
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
