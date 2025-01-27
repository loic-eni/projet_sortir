<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class SignupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un email',
                    ]),
                    new Email([
                        'message' => 'Veuillez entrer un email valide',
                    ])
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer mot de passe'],
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe',
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/',
                        'message' => 'Le mot de passe doit contenir au moins une lettre et un chiffre'
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit au moins faire 8 caractères',
                        'max' => 255,
                        'maxMessage' => 'Le mot de passe ne doit pas faire plus de 8 caractères'
                    ]),
                ]
            ])
            ->add('firstname', TextType::class ,[
                'label'=>'Prénom',
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
            ->add('phone', TextType::class, [
                'label'=>'Téléphone',
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
            ->add('save', SubmitType::class, ['label'=>'Créer le compte'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
