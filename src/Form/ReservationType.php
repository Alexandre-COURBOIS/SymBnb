<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate',DateType::class, $this->getConfig("Date d'arrivée", "Date à laquelle vous arrivez", ["widget" => "single_text"]))
            ->add('endDate',DateType::class, $this->getConfig("Date de départ", "Date à laquelle vous partez", ["widget" => "single_text"]))
            ->add('commentaire',TextareaType::class, $this->getConfig(false, "Dites-nous tout ! "))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
