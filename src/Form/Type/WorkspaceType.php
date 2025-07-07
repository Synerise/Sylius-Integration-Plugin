<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Synerise\SyliusIntegrationPlugin\Model\Workspace\AuthenticationMethod;
use Synerise\SyliusIntegrationPlugin\Model\Workspace\Environment;

final class WorkspaceType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('environment', EnumType::class, [
                'class' => Environment::class,
                'label' => 'synerise_integration.workspace.form.environment.label',
                'expanded' => true,
            ])
            ->add('authenticationMethod', EnumType::class, [
                'class' => AuthenticationMethod::class,
                'label' => 'synerise_integration.workspace.form.authentication_method.label',
                'expanded' => true,
            ])
            ->add('apiKey', TextType::class, [
                'label' => 'synerise_integration.workspace.form.api_key.label',
                'help' => 'synerise_integration.workspace.form.api_key.help.text',
                'help_translation_parameters' => [
                    '%text%' => new TranslatableMessage('synerise_integration.workspace.form.api_key.help.docs.text'),
                    '%url%' => new TranslatableMessage('synerise_integration.workspace.form.api_key.help.docs.url'),
                ],
                'help_html' => true,
                'constraints' => [
                    new NotNull([
                        'message' => 'synerise_integration.synchronization_configuration.form.product_attributes_value.errors.not_null',
                    ]),
                ],
            ])
            ->add('guid', TextType::class, [
                'label' => 'synerise_integration.workspace.form.guid.label',
                'required' => true,
                'help' => 'synerise_integration.workspace.form.guid.help.text',
                'help_translation_parameters' => [
                    '%text%' => new TranslatableMessage('synerise_integration.workspace.form.guid.help.docs.text'),
                    '%url%' => new TranslatableMessage('synerise_integration.workspace.form.guid.help.docs.url'),
                ],
                'help_html' => true,
            ])
            ->add('keepAliveEnabled', ChoiceType::class, [
                'label' => 'synerise_integration.workspace.form.keep_alive_enabled.label',
                'expanded' => true,
                'choices' => [
                    'sylius.ui.yes_label' => true,
                    'sylius.ui.no_label' => false,
                ],
            ])
            ->add('requestLoggingEnabled', CheckboxType::class, [
                'label' => 'synerise_integration.workspace.form.request_logging_enabled.label',
            ])
            ->add('liveTimeout', TextType::class, [
                'label' => 'synerise_integration.workspace.form.live_timeout.label',
            ])
            ->add('scheduledTimeout', TextType::class, [
                'label' => 'synerise_integration.workspace.form.scheduled_timeout.label',
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'synerise_integration_workspace';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'constraints' => [
                new Callback(function ($object, ExecutionContextInterface $context) {
                    if ($object->getAuthenticationMethod() === AuthenticationMethod::Basic &&
                        empty($object->getGuid())) {
                        $context->buildViolation('synerise_integration.workspace.guid.required_for_basic')
                            ->atPath('guid')
                            ->addViolation();
                    }
                }),
            ],
        ]);
    }
}
