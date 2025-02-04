<?php

declare(strict_types=1);

namespace App\Form;

use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'app.contact.name',
                    'class'       => 'contact-input-field',
                ],
                'row_attr' => [
                    'class' => 'contact-input',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your name',
                    ]),
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'app.contact.phone',
                    'class'       => 'contact-input-field',
                ],
                'row_attr' => [
                    'class' => 'contact-input',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your name',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'app.contact.email',
                    'class'       => 'contact-input-field',
                ],
                'row_attr' => [
                    'class' => 'contact-input',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a valid email',
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => false,
                'attr'  => [
                    'placeholder' => 'app.contact.message',
                    'class'       => 'contact-input-field',
                ],
                'row_attr' => [
                    'class' => 'contact-input',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your message',
                    ]),
                ],
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'contact',
                'locale'      => 'de',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
