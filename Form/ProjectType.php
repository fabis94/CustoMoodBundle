<?php

namespace CustoMood\Bundle\AppBundle\Form;

use CustoMood\Bundle\AppBundle\{
    Entity\Project, Service\AdapterService
};
use Symfony\Component\Form\{
    AbstractType, Extension\Core\Type\ChoiceType, Extension\Core\Type\TextareaType, Extension\Core\Type\TextType, FormBuilderInterface
};
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    /**
     * @var AdapterService
     */
    private $adapterService;

    /**
     * ProjectType constructor.
     * @param AdapterService $adapterService
     */
    public function __construct(AdapterService $adapterService)
    {
        $this->adapterService = $adapterService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $adapters = $this->adapterService->getAdapters();
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('adapterId', ChoiceType::class, [
                'choices' => $this->createChoiceArray($adapters),
                'expanded' => false,
                'multiple' => false,
                'label' => 'Adapter'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Project::class,
        ));
    }

    protected function createChoiceArray($adapters)
    {
        $choices = [];
        foreach ($adapters as $adapter) {
            $label = $adapter::getDisplayName() ?: $adapter::getId();
            $value = $adapter::getId();

            $choices[$label] = $value;
        }

        return $choices;
    }
}