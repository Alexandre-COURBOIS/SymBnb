<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, $this->getConfig('Ancien mot de passe', 'Saisissez votre mot de passe actuel'))
            ->add('newPassword', PasswordType::class,$this->getConfig('Nouveau mot de passe', 'Saisissez votre nouveau mot de passe'))
            ->add('confirmPassword',PasswordType::class,$this->getConfig('Confirmez votre nouveau mot de passe','Saisissez votre nouveau mot de passe'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
