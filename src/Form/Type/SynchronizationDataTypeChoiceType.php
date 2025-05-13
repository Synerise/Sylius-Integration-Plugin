<?php

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Synerise\SyliusIntegrationPlugin\Api\RequestHandlerFactory;

class SynchronizationDataTypeChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getChoices(),
            'choice_translation_domain' => false,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'synerise_integration_synchronization_data_choice';
    }

    private function getChoices(): array
    {
        return [
            'customer' => 'Customer',
            'product' => 'Product',
            'order' => 'Order',
        ];
    }
}
