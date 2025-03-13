<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Author;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => array(
                    'placeholder' => 'Write your own title!',
                )
            ])
            ->add('paragprah', TextareaType::class, [
                'attr' => array(
                    'placeholder' => 'Write your own paragraph!',
                    'style' => 'min-height: 30.75em',
                    'required' => false
                )
            ])
            ->add('imagePath', FileType::class,
                array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'Upload your own article image!',
                ),
            )
            ->add('coverPath', FileType::class,
                array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'Upload your cover image!',
                ))
            ->add('author', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
