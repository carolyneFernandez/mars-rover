<?php

namespace App\Form;

use App\Entity\ParamMap;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class ParametersMapType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('difficulty', ChoiceType::class, [
                'choices'=>[
                    'Facile'=>'Facile',
                    'Moyen'=>'Moyen',
                    'Difficile'=>'Difficile'
                ]
            ])
            ->add('glace')
            ->add('fer')
            ->add('argile')
            ->add('minerai')
            ->add('sable')
            ->add('inconnu')
            ->add('roche')
            ->setMethod('GET')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ParamMap::class,
            'csrf_protection' => false,
        ]);
    }
}
