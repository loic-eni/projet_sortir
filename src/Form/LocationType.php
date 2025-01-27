<?php
namespace App\Form;
use App\Entity\city;
use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class LocationType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom: ',
                'attr' => ['class' => 'formField'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le nom doit au moins faire 3 caractères',
                        'max' => 255,
                        'maxMessage' => 'Le nom ne peut pas dépasser 255 caractères'
                    ])
                ]
            ])
            ->add('street', null, [
                'label' => 'Rue: ',
                'attr' => ['class' => 'formField'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une rue'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le nom doit au moins faire 3 caractères',
                        'max' => 255,
                        'maxMessage' => 'Le nom ne peut pas dépasser 255 caractères'
                    ])
                ]
            ])
            ->add('lat', null,[
                'label' => 'Latitude: ',
                'attr' => ['class' => 'formField'],
                'constraints' => [
                    new Range([
                        'min' => -90,
                        'notInRangeMessage' => 'La lattitude doit être comprise entre -90 et 90',
                        'max' => 90,

                    ]),
                ]
            ])
            ->add('lng', null, [
                'label' => 'Longitude: ',
                'attr' => ['class' => 'formField'],
                'constraints' => [
                    new Range([
                        'min' => -180,
                        'notInRangeMessage' => 'La longitude doit être comprise entre -180 et 180',
                        'max' => 180,
                    ]),
                ]
            ])
            ->add('city', EntityType::class, [
                'class' => city::class,
                'label' => 'Ville: ',
                'choice_label' => 'name',
                'attr' => ['class' => 'formField'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une ville'
                    ]),
                ]
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }}
