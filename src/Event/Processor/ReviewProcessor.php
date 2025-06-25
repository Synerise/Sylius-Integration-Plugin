<?php

namespace Synerise\SyliusIntegrationPlugin\Event\Processor;

use Sylius\Component\Review\Model\ReviewInterface;
use Synerise\Sdk\Api\RequestBody\Events\AddedReviewBuilder;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Event\ReviewToProductAddReviewEvent;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\Handler\EventHandlerResolver;

class ReviewProcessor
{
    public function __construct(
        private ReviewToProductAddReviewEvent $mapper,
        private ChannelConfigurationFactory   $configurationFactory,
        private IdentityManager               $identityManager,
        private EventHandlerResolver          $eventHandlerResolver
    ) {
    }

    public function process(ReviewInterface $review): void
    {
        $configuration = $this->configurationFactory->get();
        if (!$type = $configuration?->getEventHandlerType(AddedReviewBuilder::ACTION)) {
            return;
        }

        $this->identityManager->identify($review->getAuthor()->getEmail());

        $this->eventHandlerResolver->get($type)->processEvent(
            AddedReviewBuilder::ACTION,
            $this->mapper->prepare($review, $this->identityManager->getClient()),
            $configuration->getChannel()->getId()
        );
    }
}
