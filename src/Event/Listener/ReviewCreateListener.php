<?php

namespace Synerise\SyliusIntegrationPlugin\Event\Listener;

use Psr\Log\LoggerInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\SyliusIntegrationPlugin\Event\Processor\ReviewProcessor;
use Webmozart\Assert\Assert;

class ReviewCreateListener
{
    public function __construct(
        private LoggerInterface $syneriseLogger,
        private ReviewProcessor $processor
    ) {
    }

    public function __invoke(GenericEvent $event): void
    {
        try {
            $subject = $event->getSubject();
            Assert::isInstanceOf($subject, ReviewInterface::class);
            $this->processor->process($subject);
        } catch (\Throwable $e) {
            $this->syneriseLogger->error($e);
        }
    }
}
