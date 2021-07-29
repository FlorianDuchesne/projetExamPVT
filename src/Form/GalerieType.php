<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Galerie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Image;

class GalerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('img', TextType::class, [
            //     'attr' => [
            //         'class' => 'form-control'
            //     ]
            // ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'galerie',
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
                'image_uri' => true,
                'asset_helper' => true,
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Image([
                        'maxWidth' => '1700',
                        'maxWidthMessage' => 'la largeur de l\'image ne doit pas dépasser 1700 px',
                        'minWidth' => '700',
                        'minWidthMessage' => 'la largeur de l\'image doit être supérieur à 700 pixels',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                    ])
                ],

            ])
            ->add('legende', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
            ])
            // ->add('article', EntityType::class, [
            //     'attr' => [
            //         // classe bootstrap attribuée au champ
            //         'class' => 'form-control'
            //     ],
            //     // L'entité est spécifiquement de la classe Salle
            //     'class' => Article::class,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Galerie::class,
        ]);
    }
}
