<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Location;
use App\Entity\Outing;
use App\Entity\State;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, ['label' => 'Nom'])
            ->add('startDate', null, [
                'widget' => 'single_text',
                'label' => 'Date de début'
            ])
            ->add('duration', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 8,
                    'step' => 1
                ],
                'label' => 'Durée'
            ])
            ->add('registrationMaxDate', null, [
                'widget' => 'single_text',
                'label' => 'Date d\'inscription'
            ])
            ->add('maxInscriptions', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 50,
                    'step' => 1
                ],
                'label' => 'Nombre maximum d\'inscriptions'
            ])
            ->add('outingInfo', null, ['label' => 'Informations'])
            ->add('state', EntityType::class, [
                'class' => state::class,
                'choice_label' => 'id',
                'label' => 'État'
            ])
            ->add('location', EntityType::class, [
                'class' => location::class,
                'choice_label' => 'id',
                'label' => 'Localisation'
            ])
            ->add('campus', EntityType::class, [
                'class' => campus::class,
                'choice_label' => 'id',
                'label' => 'Campus'
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
