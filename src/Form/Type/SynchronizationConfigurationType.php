<?php

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;

final class SynchronizationConfigurationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('channel', ChannelChoiceType::class, [
                'label' => 'synerise_integration.synchronization_configuration.form.channel.label'
            ])
            ->add('productAttributes', ProductAttributeChoiceType::class, [
                'label' => 'synerise_integration.synchronization_configuration.form.product_attributes.label',
                'placeholder' => 'synerise_integration.synchronization_configuration.form.product_attributes.placeholder',
                'choice_translation_domain' => true,
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'data-controller' => 'multiselect'
                ]
            ])
            ->add('productAttributeValue', ProductAttributeValueChoiceType::class, [
                'label' => 'synerise_integration.synchronization_configuration.form.product_attributes_value.label',
                'help' => 'synerise_integration.synchronization_configuration.form.product_attributes_value.help',
                'choice_translation_domain' => true,
                'constraints' => [
                    new NotNull([
                        'message' => 'synerise_integration.synchronization_configuration.product_attributes_value.not_null'
                    ])
                ],
                'expanded' => true,
                'required' => true,
                'multiple' => false
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'synerise_integration_synchronization_configuration';
    }
}
