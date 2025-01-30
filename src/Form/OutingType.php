<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Location;
use App\Entity\Outing;
use App\Repository\LocationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;

class OutingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom: ',
                'attr' => ['class' => 'formField'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le nom doit faire minimum 3 caractères',
                        'max' => 255,
                        'maxMessage' => 'Le nom ne peut pas dépasser 255 caractères'
                    ]),
                ]
            ])
            ->add('startDate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début: ',
                'attr' => ['class' => 'formField'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir la date de commencement de la sortie'
                    ]),
                    new GreaterThan([
                        'propertyPath' => 'parent.all[registrationMaxDate].data',
                        'message' => 'La date de début doit être superieur à la date d\'inscription'
                    ]),
                ]
            ])
            ->add('duration', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 480,
                    'step' => 1,
                    'class' => 'formField',
                ],
                'label' => 'Durée: ',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une durée'
                    ]),
                    new Range([
                        'min' => 1,
                        'max' => 480,
                        'notInRangeMessage' => 'Le nombre d\'inscrit doit etre compris en 1 et 50'
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]+$/',
                        'message' => 'Veuillez saisir un nombre'
                    ])
                ]
            ])
            ->add('registrationMaxDate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date d\'inscription: ',
                'attr' => ['class' => 'formField'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une date d\'inscription'
                    ])
                ]
            ])
            ->add('maxInscriptions', IntegerType::class, [
                'attr' => [
                    'min' => 2,
                    'max' => 50,
                    'step' => 1,
                    'class' => 'formField',
                ],
                'label' => 'Nombre maximum d\'inscriptions: ',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nombre'
                    ]),
                    new Range([
                        'min' => 1,
                        'max' => 50,
                        'notInRangeMessage' => 'Le nombre d\'inscrit doit etre compris en 1 et 50'
                    ])
                ]
            ])
            ->add('outingInfo', null, [
                'label' => 'Informations',
                'attr' => ['class' => 'formField'],
            ])
            ->add('location', EntityType::class, [
                'class' => location::class,
                'query_builder' => function (LocationRepository $locationRepository) {
                    return $locationRepository->createQueryBuilder('l')->andWhere('l.deletedAt IS NULL');
                },
                'choice_label' => 'name',
                'label' => 'Localisation: ',
                'required' => true,
                'attr' => ['class' => 'formField'],
            ])
            ->add('campus', EntityType::class, [
                'class' => campus::class,
                'choice_label' => 'name',
                'label' => 'Campus: ',
                'required' => true,
                'attr' => ['class' => 'formField'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,
        ]);
    }
}
