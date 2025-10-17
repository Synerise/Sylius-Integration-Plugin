<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Synerise\SyliusIntegrationPlugin\Api\EventRequestHandlerFactory;

class EventChoiceType extends AbstractType
{
    private EventRequestHandlerFactory $requestHandlerFactory;

    public function __construct(
        EventRequestHandlerFactory $requestHandlerFactory,
    ) {
        $this->requestHandlerFactory = $requestHandlerFactory;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getChoices(),
            'choice_translation_domain' => false,
            'data' => array_keys($this->getChoices()),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'synerise_integration_event_choice';
    }

    private function getChoices(): array
    {
        $options = [];
        foreach ($this->requestHandlerFactory->getHandlersPool() as $action => $requestHandler) {
            $options[$action] = $action;
        }

        return $options;
    }
}
