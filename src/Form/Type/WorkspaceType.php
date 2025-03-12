<?php

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UuidType;
use Symfony\Component\Form\FormBuilderInterface;
use Synerise\SyliusIntegrationPlugin\Model\AuthenticationMethod;
use Synerise\SyliusIntegrationPlugin\Model\Environment;

final class WorkspaceType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('environment', EnumType::class, [
                'class' => Environment::class,
                'label' => 'synerise_integration.form.workspace.environment',
            ])
            ->add('authenticationMethod', EnumType::class, [
                'class' => AuthenticationMethod::class,
                'label' => 'synerise_integration.form.workspace.authentication_method',
            ])
            ->add('apiKey', TextType::class, [
                'label' => 'synerise_integration.form.workspace.api_key',
            ])
            ->add('guid', TextType::class, [
                'label' => 'synerise_integration.form.workspace.api_guid',
                'required' => false
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'synerise_integration_workspace';
    }
}
