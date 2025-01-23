<?php

namespace App\Form;

use App\DTO\OutingFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class OutingFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, ['class' => 'App\Entity\Campus', 'choice_label'=>'name', 'required' => false])
            ->add('nameSearch', SearchType::class, ['label'=>'Le nom de la sortie contient: ', 'required'=>false])
            ->add('startsAfter', DateType::class, ['label'=>'Entre ', 'required'=>false])
            ->add('startsBefore', DateType::class, ['label'=>'et ', 'required'=>false])
            ->add('userOrganizer', CheckboxType::class, ['label'=>'Sorties dont je suis l\'organisateur/trice', 'required'=>false])
            ->add('userRegistered', CheckboxType::class, ['label'=>'Sorties auxquelles je suis inscrit/e', 'required'=>false])
            ->add('outingPast', CheckboxType::class, ['label'=>'Sorties passées', 'required'=>false])
            ->add('find', SubmitType::class, ['label'=>'Rechercher']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OutingFilter::class,
        ]);
    }
}
