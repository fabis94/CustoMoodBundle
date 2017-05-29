<?php

namespace CustoMood\Bundle\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClearAllAdapterMetadataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('clear', SubmitType::class, [
                'label' => 'Clear',
                'attr' => [
                    'class' => 'outline'
                ]
            ]);
    }

}