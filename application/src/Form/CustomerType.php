<?php

namespace App\Form;

use App\Entity\Customer;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Vorname',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nachname',
            ])
            ->add('companyName', TextType::class, [
                'label' => 'Firmenname',
            ])
            ->add('street', TextType::class, [
                'label' => 'Straße',
            ])
            ->add('housenumber', TextType::class, [
                'label' => 'Hous',
            ])
            ->add('plz', TextType::class, [
                'label' => 'PLZ',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ort',
            ])
            ->add('isActive', ChoiceType::class, [
                'choices' => ['ja' => true, 'nein' => false],
                'label' => 'Aktiv',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
