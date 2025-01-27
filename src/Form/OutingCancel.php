<?php
 
namespace App\Form;
 
use App\Entity\Outing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class OutingCancel extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('reason', null, [
                'label' => "Motif de l'annulation :",
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez donner un motif d'annulation"
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le motif d\'annulation doit faire plus de 3 caractères',
                        'max' => 4000,
                        'maxMessage' => 'Le motif d\'annulation doit faire main de 4000 caractères',
                    ])
                ]
            ]);
 
    }
 
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,
        ]);
    }
}