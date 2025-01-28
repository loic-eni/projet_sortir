<?php

namespace App\Form;

use App\DTO\OutingFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
            ->add('campus', EntityType::class, ['class' => 'App\Entity\Campus', 'choice_label'=>'name', 'required' => false, 'attr'=>['class'=>'select select-primary select-sm max-w-xs'], 'label'=>'Site: '])
            ->add('nameSearch', SearchType::class, ['label'=>'Le nom de la sortie contient: ', 'required'=>false])
            ->add('startsAfter', DateTimeType::class, ['label'=>'Entre ', 'required'=>false])
            ->add('startsBefore', DateTimeType::class, ['label'=>'et ', 'required'=>false])
            ->add('userOrganizer', ChoiceType::class, ['label'=>'Sorties dont je suis l\'organisateur/trice: ', 'choices'=>['Oui'=>true, 'Non'=>false], 'attr'=>['class'=>'select select-primary select-sm max-w-xs'], 'required'=>false])
            ->add('userRegistered', ChoiceType::class, ['label'=>'Sorties auxquelles je suis inscrit/e: ', 'choices'=>['Oui'=>true, 'Non'=>false], 'attr'=>['class'=>'select select-primary select-sm max-w-xs'], 'required'=>false])
            ->add('outingPast', ChoiceType::class, ['label'=>'Sorties passées: ', 'choices'=>['Oui'=>true, 'Non'=>false], 'attr'=>['class'=>'select select-primary select-sm max-w-xs'], 'required'=>false])
            ->add('find', SubmitType::class, ['label'=>'Rechercher']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OutingFilter::class,
            'method' => 'GET'
        ]);
    }
}
