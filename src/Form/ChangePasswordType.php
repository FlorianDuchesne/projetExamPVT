<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => array('label' => 'Nouveau mot de passe'),
                'second_options' => array('label' => 'Confirmation du nouveau mot de passe'),
                'invalid_message' => 'Les mots de passe ne correspondent pas !',
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => "Veuillez mettre plus de {{ limit }} caractères",
                        'max' => 15,
                        'maxMessage' => "Veuillez mettre moins de {{ limit }} caractères"
                    ])
                ]
            ])
            // ->add('token', HiddenType::class, [
            //     'data' => 'NULL',
            // ])
            ->add('valider', SubmitType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
