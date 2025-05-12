<?php

namespace Synerise\SyliusIntegrationPlugin\EventHandler;

class EventHandlerFactory
{
    /**
     * @param iterable<string, EventHandlerInterface> $handlers
     */
    public function __construct(
        private iterable $handlers
    ) {
    }

    public function getHandlerByType(string $type): EventHandlerInterface
    {
        foreach ($this->handlers as $id => $handler) {
            if ($id === $type) {
                return $handler;
            }
        }

        throw new \InvalidArgumentException("Handler not found for key: $type");
    }
}
