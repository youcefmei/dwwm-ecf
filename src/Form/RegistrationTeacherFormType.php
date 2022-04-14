<?php

namespace App\Form;

use App\Entity\Teacher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationTeacherFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => "Nom",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom.',
                    ]),

                ],
            ],)
            ->add('firstname', TextType::class, [
                'label' => "Prénom",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un prénom.',
                    ]),

                ],
            ],)
            ->add('description', TextareaType::class, [
                'label' => "Décrivez votre parcours ainsi que vos domaines de compétences",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une description.',
                    ]),
                    new Length([
                        'min' => 200, 'minMessage' => "Votre description doit contenir un minimum de {{ limit }} caractères",
                        'max' => 3250, 'maxMessage' => "Votre description doit contenir au maximum {{ limit }} caractères",
                    ]),

                ],
            ],)
            ->add('imageFile', VichImageType::class,[
                'label' => "Une photo de profil",]
                )
            ->add("user", RegistrationFormType::class, ['label' => ""])
            ->add('save', SubmitType::class, [
                'label' => "S'inscrire",
                "attr"=>["class"=>"btn text-white"]
                
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Teacher::class,
        ]);
    }
}
