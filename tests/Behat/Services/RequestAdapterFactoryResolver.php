<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Services;

use Microsoft\Kiota\Abstractions\Authentication\AuthenticationProvider;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Microsoft\Kiota\Abstractions\Serialization\ParseNodeFactory;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriterFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Synerise\Sdk\Api\Config;
use Synerise\Sdk\Guzzle\RequestAdapterFactoryInterface;

final class RequestAdapterFactoryResolver implements RequestAdapterFactoryInterface
{
    public function __construct(
        private RequestAdapterFactoryInterface $realFactory,
        private RequestAdapterFactoryInterface $mockFactory,
        private RequestStack $requestStack,
    ) {
    }

    public function create(
        Config $config,
        AuthenticationProvider $authenticationProvider,
        array $middlewares = [],
        ?ParseNodeFactory $parseNodeFactory = null,
        ?SerializationWriterFactory $serializationWriterFactory = null
    ): RequestAdapter
    {
        $factory = $this->getFactory();
        return $factory->create($config, $authenticationProvider, $middlewares, $parseNodeFactory, $serializationWriterFactory);
    }

    private function getFactory(): RequestAdapterFactoryInterface
    {
        if ($this->shouldUseMock()) {
            return $this->mockFactory;
        }

        return $this->realFactory;
    }

    private function shouldUseMock(): bool
    {
        if ($this->requestStack->getCurrentRequest()->cookies->get('e2e') !== null) {
            return false;
        }

        return true;
    }
}
