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
                'label' => 'synerise_integration.ui.workspace'
            ])
            ->add('trackingEnabled', CheckboxType::class, [
                'label' => 'synerise_integration.ui.',
                'required' => false
            ])
            ->add('opengraphEnabled', CheckboxType::class, [
                'label' => 'Render OG tags from page visit events',
                'required' => false
            ])
            ->add('virtualPage', CheckboxType::class, [
                'label' => 'Dynamic content for PWA, SPA sites',
                'help' => 'Enabling this option lets you display Synerise dynamic content on single page or progressive web application.',
                'required' => false
            ])
            ->add('cookieDomainEnabled', CheckboxType::class, [
                'label' => 'Override cookie domain',
                'help' => 'Declare a specific domain for cookies if several subdomains share a single workspace.',
                'required' => false,
                'mapped' => false,
            ])
            ->add('cookieDomain', TextType::class, [
                'label' => 'Declare a specific domain/root domain',
                'attr'=> [
                    'placeholder' => 'domain.com',
                ],
                'required' => false
            ])
            ->add('customPageVisit', CheckboxType::class, [
                'label' => 'Custom page visit implementation ',
                'help' => 'Recommended for PWA themes. Enabling this option requires code changes. For more information check the documentation.',
                'required' => false
            ])
            ->add('events', EventChoiceType::class, [
                'label' => 'synerise_integration.ui.events',
                'choice_translation_domain' => true,
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'data-controller' => 'multiselect'
                ]
            ])
            ->add('queueEvents', EventChoiceType::class, [
                'label' => 'synerise_integration.ui.queue_events',
                'choice_translation_domain' => true,
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
