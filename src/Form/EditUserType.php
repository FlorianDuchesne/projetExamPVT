<?php

namespace App\Form;

use App\Entity\User;
use App\Form\PlaceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // A réviser !!!! (date de naissance notamment)

        $builder
            ->add('imageFile', VichImageType::class, [
                'label' => 'Avatar',
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
                'image_uri' => true,
                'asset_helper' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('pseudo', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'name' => 'text',
                    'oninput' => 'this.style.height = "";this.style.height = this.scrollHeight + "px"'
                ]
            ])
            ->add('places', CollectionType::class, [
                'error_bubbling' => false,
                // là aussi, nous voulons un champ de type CollectionType
                'label' => "Voyages",
                // Il n'a pas de label
                'entry_type' => PlaceType::class,
                // Le type de chaque élément de la collection sera un formulaire imbriqué, ProgrammerType
                'entry_options' => [
                    // Il n'y a pas de label donné ici à chaque formulaire qui sera imbriqué
                    'label' => false,
                ],
                // on autorise plusieurs ajouts à la fois
                'allow_add' => true,
                // on autorise la suppression d'ajouts en cours
                'allow_delete' => true,
                // by_reference mis à false sert ici à cloner l'objet 
                //pour s'assurer qu'on appellera bien le setter de l'objet parent.
                // Cela nous permet donc de bien appeler les méthodes désirées, comme les getters et setters.
                'by_reference' => false,
                // le champ est requis
                'required' => false,
                // 'mapped' => false
            ])
            // ->add('voyagesAccomplis', CollectionType::class, [
            //     'error_bubbling' => false,
            //     // là aussi, nous voulons un champ de type CollectionType
            //     'label' => false,
            //     // Il n'a pas de label
            //     'entry_type' => PlaceType::class,
            //     // Le type de chaque élément de la collection sera un formulaire imbriqué, ProgrammerType
            //     'entry_options' => [
            //         // Il n'y a pas de label donné ici à chaque formulaire qui sera imbriqué
            //         'label' => false,
            //     ],
            //     // on autorise plusieurs ajouts à la fois
            //     'allow_add' => true,
            //     // on autorise la suppression d'ajouts en cours
            //     'allow_delete' => true,
            //     // by_reference mis à false sert ici à cloner l'objet 
            //     //pour s'assurer qu'on appellera bien le setter de l'objet parent.
            //     // Cela nous permet donc de bien appeler les méthodes désirées, comme les getters et setters.
            //     'by_reference' => false,
            //     // le champ est requis
            //     'required' => false,
            //     // 'mapped' => false
            // ])
            ->add('date_naissance', DateType::class, [
                'label' => 'Date de naissance (vous devez avoir au moins treize ans pour vous inscrire)',
                'attr' => [
                    'class' => 'form-control'
                ],
                'widget' => 'single_text',
                'constraints' => [
                    new Range([
                        'min' => "now - 100 years",
                        'max' => "now - 13 years",
                        'minMessage' => "minMessage",
                        'maxMessage' => "maxMessage",
                    ])
                ],
            ])
            // ->add('date_creation', HiddenType::class, [
            //     'mapped' => false,
            // ])
            ->add('envoyer', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success m-3'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
