<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image as AssertImage;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', TextType::class, [
            'label' => 'Titre de l\'article (FR)',
            'attr' => ['class' => 'form-control']
        ])
        ->add('content', TextareaType::class, [
            'label' => 'Contenu de l\'article (FR)',
            'attr' => ['class' => 'form-control', 'rows' => 6]
        ])
        ->add('titleEn', TextType::class, [
            'label' => 'Titre de l\'article (EN)',
            'required' => false,
            'attr' => ['class' => 'form-control', 'placeholder' => 'English title']
        ])
        ->add('contentEn', TextareaType::class, [
            'label' => 'Contenu de l\'article (EN)',
            'required' => false,
            'attr' => ['class' => 'form-control', 'rows' => 6, 'placeholder' => 'English content']
        ])
        ->add('category', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'name',
            'label' => 'Catégorie',
            'attr' => ['class' => 'form-control']
        ])
        ->add('picture', FileType::class, [
            'label' => 'Image de l\'article',
            'required' => false,
            'mapped' => false, // Ne pas mapper directement avec l'entité
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new AssertImage([
                    'maxSize' => '2M', 
                    'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'], 
                    'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG ou GIF).',
                ])
            ],
        ])
        ->add('publishedAt', DateTimeType::class, [
            'widget' => 'single_text', 
            'required' => false,
            'label' => 'Date de publication',
            'attr' => ['class' => 'form-control'],
        ])
        ->add('save', SubmitType::class, [
            'label' => 'Enregistrer l\'article',
            'attr' => ['class' => 'btn btn-primary mt-3']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
