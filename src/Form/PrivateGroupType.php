<?php

namespace App\Form;

use App\Entity\PrivateGroup;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrivateGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label'=>'Nom du groupe: ',
                'attr'=>[
                    'class'=>'input input-sm input-bordered w-full max-w-xs'
                ]
            ])
            ->add('whiteListedUsers', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'multiple' => true,
                'label'=>'Utilisateurs: ',
                'expanded' => true,
                'attr'=>[
                    'class'=>'card-bordered max-h-[250px] w-full overflow-y-scroll'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrivateGroup::class,
        ]);
    }
}
