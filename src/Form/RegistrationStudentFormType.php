<?php

namespace App\Form;

use App\Entity\Student;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\Validator\Constraints\Valid;

class RegistrationStudentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => "Votre Pseudo",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom d\'utilisateur.',
                    ]),
                    new Length([
                        'min' => 3, 'minMessage' => "Votre nom d'utilisateur doit contenir un minimum de {{ limit }} caractères",
                        'max' => 250, 'maxMessage' => "Votre nom d'utilisateur doit contenir au maximum {{ limit }} caractères",
                    ]),
                    // new Unique(['message' => "Ce nom d'utilisateur éxiste déjà"]),
                    new Regex(['pattern' => '/^[a-z_0-9]+$/i', 'message' => "Les caractères autorisés sont les lettres , les chiffres ainsi que l'underscore"]),
                ],
            ],)
            ->add("user",RegistrationFormType::class,['label' => " ",
                  
                ])
            ->add('save', SubmitType::class, [
                'label' => "s'inscrire",'attr'=>['class'=>'btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
