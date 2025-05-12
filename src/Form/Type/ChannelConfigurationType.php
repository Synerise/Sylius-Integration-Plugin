<?php

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ChannelConfigurationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('channel', ChannelChoiceType::class, [
                'label' => 'sylius.ui.channel'
            ])
            ->add('workspace', WorkspaceChoiceType::class, [
                'label' => 'synerise_integration.ui.workspace'
            ])
            ->add('trackingEnabled', CheckboxType::class, [
                'label' => 'synerise_integration.ui.tracking_enabled',
                'required' => false
            ])
            ->add('cookieDomain', TextType::class, [
                'label' => 'synerise_integration.ui.cookie_domain',
                'required' => false
            ])
            ->add('customPageVisit', CheckboxType::class, [
                'label' => 'synerise_integration.ui.custom_page_visit',
                'required' => false
            ])
            ->add('virtualPage', CheckboxType::class, [
                'label' => 'synerise_integration.ui.virtual_page',
                'required' => false
            ])
            ->add('opengraphEnabled', CheckboxType::class, [
                'label' => 'synerise_integration.ui.opengraph_enabled',
                'required' => false
            ])
            ->add('events', EventChoiceType::class, [
                'label' => 'synerise_integration.ui.events',
                'multiple' => true,
                'required' => false
            ])
            ->add('queueEvents', EventChoiceType::class, [
                'label' => 'synerise_integration.ui.queue_events',
                'multiple' => true,
                'required' => false
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'synerise_integration_channel_configuration';
    }
}
