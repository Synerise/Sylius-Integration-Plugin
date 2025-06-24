<?php

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ChannelConfigurationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('channel', ChannelChoiceType::class, [
                'label' => 'sylius.ui.channel'
            ])
            ->add('workspace', WorkspaceChoiceType::class, [
                'label' => 'synerise_integration.ui.channel_configuration.form.workspace.label'
            ])
            ->add('trackingEnabled', CheckboxType::class, [
                'label' => 'synerise_integration.channel_configuration.form.tracking_enabled.label',
                'required' => false
            ])
            ->add('opengraphEnabled', CheckboxType::class, [
                'label' => 'Render OG tags from page visit events',
                'required' => false
            ])
            ->add('virtualPage', CheckboxType::class, [
                'label' => 'synerise_integration.channel_configuration.form.virtual_page.label',
                'help' => 'synerise_integration.channel_configuration.form.virtual_page.help',
                'required' => false
            ])
            ->add('cookieDomainEnabled', CheckboxType::class, [
                'label' => 'synerise_integration.channel_configuration.form.cookie_domain_enabled.label',
                'help' => 'synerise_integration.channel_configuration.form.cookie_domain_enabled.help',
                'required' => false,
                'mapped' => false,
            ])
            ->add('cookieDomain', TextType::class, [
                'label' => 'synerise_integration.channel_configuration.form.cookie_domain.label',
                'attr'=> [
                    'placeholder' => 'domain.com',
                ],
                'required' => false
            ])
            ->add('customPageVisit', CheckboxType::class, [
                'label' => 'synerise_integration.channel_configuration.form.custom_page_visit.label',
                'help' => 'synerise_integration.channel_configuration.form.custom_page_visit.help',
                'required' => false
            ])
            ->add('events', EventChoiceType::class, [
                'label' => 'synerise_integration.channel_configuration.form.events.label',
                'help' => 'synerise_integration.channel_configuration.form.events.help',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'data-controller' => 'multiselect'
                ]
            ])
            ->add('snrsParamsEnabled', CheckboxType::class, [
                'label' => 'synerise_integration.channel_configuration.form.snrs_params_enabled.label',
                'help' => 'synerise_integration.channel_configuration.form.snrs_params_enabled.help',
            ])
            ->add('queueEvents', EventChoiceType::class, [
                'label' => 'synerise_integration.channel_configuration.form.queue_events.label',
                'help' => 'synerise_integration.channel_configuration.form.queue_events.help',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'data-controller' => 'multiselect'
                ]
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (isset($data['cookieDomainEnabled']) && !$data['cookieDomainEnabled']) {
                $data['cookieDomain'] = null;
                $event->setData($data);
            }
        });

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if ($data) {
                $form->get('cookieDomainEnabled')->setData($data->getCookieDomain() !== null);
            }
        });

    }

    public function getBlockPrefix(): string
    {
        return 'synerise_integration_channel_configuration';
    }
}
