<?php

namespace CustoMood\Bundle\AppBundle\Form;

use CustoMood\Bundle\AppBundle\Model\AdapterWebListingItem;
use CustoMood\Bundle\AppBundle\Model\ToggleAdapters;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ToggleAdaptersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('adapters', CollectionType::class, [
                'entry_type' => AdapterWebListingItemType::class
            ])
            ->add('save', SubmitType::class, array('label' => 'Save'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ToggleAdapters::class,
        ));
    }
}