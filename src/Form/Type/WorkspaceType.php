<?php

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Grid\Filter\BooleanFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Synerise\SyliusIntegrationPlugin\Model\AuthenticationMethod;
use Synerise\SyliusIntegrationPlugin\Model\Environment;

final class WorkspaceType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('environment', EnumType::class, [
                'class' => Environment::class,
                'label' => 'synerise_integration.form.workspace.environment.label',
                'expanded' => true
            ])
            ->add('authenticationMethod', EnumType::class, [
                'class' => AuthenticationMethod::class,
                'label' => 'synerise_integration.form.workspace.authentication_method.label',
                'expanded' => true
            ])
            ->add('apiKey', TextType::class, [
                'label' => 'synerise_integration.form.workspace.api_key.label',
                'constraints' => [
                    new NotNull([
                        'message' => 'synerise_integration.ui.synchronization_configuration.form.product_attributes_value.errors.not_null'
                    ])
                ],
            ])
            ->add('guid', TextType::class, [
                'label' => 'synerise_integration.form.workspace.guid.label',
                'required' => true
            ])
            ->add('keepAliveEnabled',ChoiceType::class, [
                'label' => 'synerise_integration.form.workspace.keep_alive_enabled.label',
                'expanded' => true,
                'choices' => [
                    'sylius.ui.yes_label' => BooleanFilter::TRUE,
                    'sylius.ui.no_label' => BooleanFilter::FALSE,
                ],
            ])->add('liveTimeout',TextType::class, [
                'label' => 'synerise_integration.form.workspace.live_timeout.label'
            ])->add('scheduledTimeout',TextType::class, [
                'label' => 'synerise_integration.form.workspace.scheduled_timeout.label'
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
                    if ($object->getAuthenticationMethod() === AuthenticationMethod::Basic
                        && empty($object->getGuid())) {
                        $context->buildViolation('synerise_integration.workspace.guid.required_for_basic')
                            ->atPath('guid')
                            ->addViolation();
                    }
                }),
            ],
        ]);
    }
}
