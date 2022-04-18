<?php

namespace App\Form;

use App\Entity\Lesson;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',options:['label'=>"Titre",'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un titre.',
                ]),
            ]])
            ->add('content', CKEditorType::class,['label'=>"Contenu (Edition temporaire simple)",'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer du texte.',
                ]),
            ]])
            ->add('media',options:['label'=>"Video",'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un lien.',
                ]),
            ]])
            ->add('is_published',options:['label'=>"Publier ?"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);
    }
}
