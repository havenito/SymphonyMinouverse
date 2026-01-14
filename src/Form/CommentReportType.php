<?php

namespace App\Form;

use App\Entity\CommentReport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reportCategory', ChoiceType::class, [
                'label' => 'Catégorie de signalement',
                'choices' => [
                    'Contenu inapproprié' => 'inappropriate_content',
                    'Spam' => 'spam',
                    'Harcèlement' => 'harassment',
                    'Langage offensant' => 'offensive_language',
                    'Fausses informations' => 'misinformation',
                    'Autre' => 'other'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('reason', TextareaType::class, [
                'label' => 'Raison du signalement (optionnel)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Décrivez pourquoi vous signalez ce commentaire...'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Signaler le commentaire',
                'attr' => ['class' => 'btn btn-danger']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CommentReport::class,
        ]);
    }
}