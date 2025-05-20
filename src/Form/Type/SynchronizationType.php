<?php

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

final class SynchronizationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('channel', ChannelChoiceType::class, [
                'label' => 'sylius.ui.channel'
            ])
            ->add('dataTypes', SynchronizationDataTypeChoiceType::class, [
                'label' => 'synerise_integration.ui.synchronization_data_types',
                'choice_translation_domain' => true,
                'multiple' => true,
                'required' => false
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'synerise_integration_synchronization';
    }
}
