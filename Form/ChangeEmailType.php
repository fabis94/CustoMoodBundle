<?php

namespace CustoMood\Bundle\AppBundle\Form;

use CustoMood\Bundle\AppBundle\Model\ChangeEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ChangeEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('newEmail', EmailType::class)
            ->add('save_email', SubmitType::class, array('label' => 'Change email'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ChangeEmail::class,
        ));
    }
}