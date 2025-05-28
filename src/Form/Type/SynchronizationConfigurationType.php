<?php

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Synerise\SyliusIntegrationPlugin\Entity\ProductAttributeValue;

final class SynchronizationConfigurationType extends AbstractResourceType
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
            ->add('productAttributes', ProductAttributeChoiceType::class, [
                'label' => 'synerise_integration.ui.synchronization_product_attributes',
                'choice_translation_domain' => true,
                'multiple' => true,
                'required' => false
            ])
            ->add('productAttributeValue', EnumType::class, [
                'class' => ProductAttributeValue::class,
                'label' => 'synerise_integration.ui.synchronization_product_attribute_value',
                'choice_translation_domain' => true
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'synerise_integration_synchronization_configuration';
    }
}
