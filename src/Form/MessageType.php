<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Message;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('received', EntityType::class, [
                'attr' => [
                    'class' => 'form-control w-100 js-basic-single'
                ],
                'label' => 'Chercher votre destinataire :',
                'class' => User::class,
                // 'multiple' => false,
                // 'choice_label' => 'pseudo',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.pseudo', 'ASC');
                }
                // 'by_reference' => false
            ])
            ->add('texte', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control mt-3'
                ],
                'label' => false,
            ])
            ->add('envoyer', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success m-3'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
