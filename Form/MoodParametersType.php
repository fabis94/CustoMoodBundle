<?php

namespace CustoMood\Bundle\AppBundle\Form;

use CustoMood\Bundle\AppBundle\Model\MoodParameters;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoodParametersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('moodFrom', DateType::class, [
                'model_timezone' => 'UTC',
                'label' => 'Date From',
                'widget' => 'single_text',
                'attr' => ['class' => 'date-input']
            ])
            ->add('moodTo', DateType::class, [
                'model_timezone' => 'UTC',
                'label' => 'Date To',
                'widget' => 'single_text',
                'attr' => ['class' => 'date-input']
            ])
            ->add('aggregatePeriod', ChoiceType::class, [
                'choices' => array_flip(MoodParameters::AGGREGATE_PERIODS_IN_DAYS),
                'label' => 'Aggregate period'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => MoodParameters::class,
        ));
    }
}