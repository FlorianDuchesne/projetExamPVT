<?php

namespace App\Form;

use App\Entity\Place;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', HiddenType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'nom du lieu'
                ],
                'required' => true,
            ])
            ->add('placeId', HiddenType::class, [
                'required' => true,
            ])
            ->add('statut', ChoiceType::class, [
                'required' => true,
                'label' => 'Statut :',
                'choices' => [
                    'voyage réalisé' => true,
                    'voyage souhaité' => false,
                ],
            ]);
        // ->add('statut', CheckboxType::class, [
        //     'required' => false,
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
