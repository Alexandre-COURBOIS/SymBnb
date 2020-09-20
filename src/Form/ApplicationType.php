<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType
{

    /**
     *
     * Permet d'avoir la configuration de base d'un champ Type
     *
     * @param $label
     * @param $placeholder
     * @param array $options
     * @return array
     *
     */
    protected function getConfig($label, $placeholder, array $options = [])
    {
        return array_merge([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ], $options);
    }

}