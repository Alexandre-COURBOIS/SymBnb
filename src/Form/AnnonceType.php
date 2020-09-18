<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceType extends AbstractType
{
    /**
     * Permet de setup le label et attr dans le builder
     *
     * @param string $label
     * @param string $placeholder
     *
     * @return array
     */
    private function getConfig($label,$placeholder) {
        return [
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,$this->getConfig('Titre','Tapez un titre pour votre annonce !'))
            ->add('coverImage',UrlType::class, $this->getConfig('url de l\'image principale','Saisir l\'url de votre image'))
            ->add('introduction',TextType::class, $this->getConfig('Introduction','Donnez une description globale de l\'annonce'))
            ->add('content', TextareaType::class, $this->getConfig('Description détaillée','Tapez une description digne de ce nom !'))
            ->add('rooms', IntegerType::class, $this->getConfig('Nombre de chambres','Le nombres de chambres'))
            ->add('price', MoneyType::class, $this->getConfig('Prix par nuit','Indiquez le prix que vous voulez'));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
