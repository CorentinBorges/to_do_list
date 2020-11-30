<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['is_edit']) {
            $builder
                ->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
                ->add('password', PasswordType::class, [
                    'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                    'required' => false,
                    'label' => 'Mot de passe',
                    'mapped'=>false,
                    'help' => "Si aucun mot de passe n'est entré, le mot de passe d'origine est sauvegardé"
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Adresse email',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('roleUser', CheckboxType::class,[
                    'mapped' => false,
                    'required' => false,
                    'label' => 'User',
                    'label_attr' => ['class' => 'mr-2'],
                    'attr' => ['checked' => 'checked',]
                ])
                ->add('roleAdmin', CheckboxType::class,[
                    'mapped' => false,
                    'required' => false,
                    'label' => 'Admin',
                    'label_attr' => ['class' => 'mr-2'],
                    'attr' => ['class' => 'false' ]
                ]);
        } else{
            $builder
                ->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                    'required' => true,
                    'first_options'  => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Tapez le mot de passe à nouveau'],
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Adresse email',
                    'label_attr' => ['class' => 'mr-2'],
                ])
                ->add('roleUser', CheckboxType::class,[
                    'mapped' => false,
                    'required' => false,
                    'label' => 'User',
                    'label_attr' => ['class' => 'mr-2'],
                    'attr' => ['checked' => 'checked',]
                ])
                ->add('roleAdmin', CheckboxType::class,[
                    'mapped' => false,
                    'required' => false,
                    'label' => 'Admin',
                    'label_attr' => ['class' => 'mr-2'],
                    'attr' => ['class' => 'false' ]
                ]);
        }

    }

    public function configureOptions(OptionsResolver $options)
    {
        $options->setDefaults([
            'is_edit'=>false
        ]);
        $options->setAllowedTypes('is_edit', 'bool');
    }
}
