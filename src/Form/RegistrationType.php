<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfig("Prénom", "Votre prenom"))
            ->add('lastName', TextType::class, $this->getConfig("Nom","Votre nom"))
            ->add('email', EmailType::class, $this->getConfig("Email", "Votre email"))
            ->add('picture', UrlType::class, $this->getConfig("Photo de profil", "Url de votre avatar"))
            ->add('password', PasswordType::class, $this->getConfig("Mot de passe", "Votre mot de passe"))
            ->add('introduction', TextType::class, $this->getConfig("Introduction", "Presentez vous en quelques mots"))
            ->add('description', TextareaType::class, $this->getConfig("Description détaillée", "Présentez-vous !"));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
