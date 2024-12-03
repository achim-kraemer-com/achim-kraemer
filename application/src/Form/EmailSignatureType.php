<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\EmailSignature;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailSignatureType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label'      => 'app.email_signature.title_text',
                'label_attr' => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
            ])
            ->add('image', FileType::class, [
                'label'       => 'app.email_signature.image',
                'mapped'      => false,
                'required'    => false,
                'constraints' => [
                    new File([
                        'maxSize'   => '2M',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => $this->translator->trans('app.email_signature.please_upload_image_file'),
                    ]),
                ],
                'label_attr' => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
            ])
            ->add('link', UrlType::class, [
                'label_attr' => [
                    'class' => 'custom-label',
                ],
                'attr' => [
                    'class' => 'form-text',
                ],
                'label'    => 'app.email_signature.link',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EmailSignature::class,
        ]);
    }
}
