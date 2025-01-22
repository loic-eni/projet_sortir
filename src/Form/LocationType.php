<?php

namespace App\Form;

use App\Entity\city;
use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom: ',
                'attr' => ['class' => 'formField'],
            ])
            ->add('street', null, [
                'label' => 'Rue: ',
                'attr' => ['class' => 'formField'],
            ])
            ->add('lat', null,[
                'label' => 'Latitude: ',
                'attr' => ['class' => 'formField'],
            ])
            ->add('lng', null, [
                'label' => 'Longitude: ',
                'attr' => ['class' => 'formField'],
            ])
            ->add('city', EntityType::class, [
                'class' => city::class,
                'label' => 'Ville: ',
                'choice_label' => 'name',
                'attr' => ['class' => 'formField'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
