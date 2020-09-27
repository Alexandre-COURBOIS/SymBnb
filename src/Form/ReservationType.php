<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends ApplicationType
{
    private $transformer;

    public function __construct(FrenchToDateTimeTransformer $transformer) {
        $this->transformer = $transformer;
    }

    public function builderTransformer(FormBuilderInterface $builder, $value) {

        $builder->get($value)->addModelTransformer($this->transformer);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate',TextType::class, $this->getConfig("Date d'arrivée", "Date à laquelle vous arrivez"))
            ->add('endDate',TextType::class, $this->getConfig("Date de départ", "Date à laquelle vous partez"))
            ->add('commentaire',TextareaType::class, $this->getConfig(false, "Dites-nous tout ! ", ["required" => false]))
        ;

        $this->builderTransformer($builder,'startDate');
        $this->builderTransformer($builder,'endDate');

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
