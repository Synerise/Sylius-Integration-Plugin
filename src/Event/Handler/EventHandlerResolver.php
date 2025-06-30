<?php

namespace Synerise\SyliusIntegrationPlugin\Event\Handler;

class EventHandlerResolver
{
    /**
     * @param iterable<string, EventHandlerInterface> $handlers
     */
    public function __construct(
        private iterable $handlers
    ) {
    }

    public function get(string $type): EventHandlerInterface
    {
        foreach ($this->handlers as $id => $handler) {
            if ($id === $type) {
                return $handler;
            }
        }

        throw new \InvalidArgumentException("Handler not found for key: $type");
    }
}
