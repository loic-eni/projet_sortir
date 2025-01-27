<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\File;

class EditAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'input input-bordered w-full max-w-xs'],
                'empty_data' => '',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un email',
                    ]),
                    new Email([
                        'message' => 'Veuillez entrer un email valide',
                    ])
                ]
            ])
            ->add('firstname', TextType::class ,[
                'label'=>'Prénom',
                'attr' => ['class' => 'input input-bordered w-full max-w-xs'],
                'empty_data' => '',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un prénom',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le prénom doit au moins faire 2 caractères',
                        'max' => 255,
                        'maxMessage' => 'Le prénom doit faire moins de 255 caractères',
                    ])
                ]
            ])
            ->add('lastname', TextType::class, [
                'label'=>'Nom',
                'attr' => ['class' => 'input input-bordered w-full max-w-xs'],
                'empty_data' => '',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom doit au moins faire 2 caractères',
                        'max' => 255,
                        'maxMessage' => 'Le nom doit faire moins de 255 caractères',
                    ])
                ]
            ])
            ->add('imgPath', FileType::class, [
                'label' => 'Image PNG/JPEG : ',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'file-input w-full max-w-xs my-2'],
                'constraints' => [
                    new File([
                        'maxSize' => '1000k',
                        'mimeTypes' => [
                            'image/*',
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PNG/JPEG image',
                    ])
                ]
            ])
            ->add('phone', TextType::class, [
                'label'=>'Téléphone',
                'attr' => ['class' => 'input input-bordered w-full max-w-xs'],
                'empty_data' => '',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un numero de téléphone',
                    ]),
                    new Regex([
                        'pattern' => '/^\+?[0-9\s\-\(\)]{7,15}$/',
                        'message' => 'Veuillez entrer un numero de telephone valide'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
