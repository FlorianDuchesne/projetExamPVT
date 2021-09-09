<?php

namespace App\Form;

use App\Entity\Pays;
use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Article;
use App\Entity\Hashtag;
use App\Form\GalerieType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'ex: Quelques jours à Kyoto'
                ]
            ])
            ->add('texte', CKEditorType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true,
            ])
            ->add('galeries', CollectionType::class, [
                // 'constraints' => [
                //     new Count(
                //         [
                //             'max' => 5,
                //             'maxMessage' => 'il ne peut y avoir que cinq images maximum par article'
                //         ]
                //     ),
                // ],
                'error_bubbling' => false,
                // là aussi, nous voulons un champ de type CollectionType
                'label' => false,
                // Il n'a pas de label
                'entry_type' => GalerieType::class,
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
                'required' => true,

            ])
            ->add('lieu', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'ex: Kyoto'
                ]
            ])
            ->add('theme', EntityType::class, [
                'attr' => [
                    // classe bootstrap attribuée au champ
                    'class' => 'form-control'
                ],
                'class' => Theme::class,
            ])
            ->add('pays', EntityType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'class' => Pays::class,
            ])
            ->add('hashtags', EntityType::class, [
                'attr' => [
                    'class' => 'form-control js-basic-multiple'
                ],
                'class' => Hashtag::class,
                'required' => false,
                'multiple' => true,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'by_reference' => false
            ])
            ->add('brouillon', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-warning mt-3 mb-3'
                ],
                'label' => 'Enregistrer comme brouillon'
            ])
            ->add('Publier', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success mb-3'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
