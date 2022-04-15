<?php

namespace App\Form;

use App\Entity\Lesson;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',options:['label'=>"Titre"])
            ->add('content', CKEditorType::class,['label'=>"Contenu (Editeur simple)"])
            ->add('media',options:['label'=>"Video"])
            ->add('is_published',options:['label'=>"Publier ?"])
            // ->add('slug')
            // ->add('update_at')
            // ->add('section')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);
    }
}
