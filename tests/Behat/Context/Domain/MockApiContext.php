<?php

declare(strict_types=1);

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Step\Given;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\Mock\HandlerQueueFactory;

final class MockApiContext extends RawMinkContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    #[Given('api response(s) will be mocked with:')]
    public function apiResponsesWillBeMockedWith(TableNode $table): void
    {
        $value = json_encode(array_keys($table->getRowsHash()));

        $this->sharedStorage->set(HandlerQueueFactory::MOCK_HANDLER_QUEUE_COOKIE, $value);
        $this->getSession()->setCookie(
            HandlerQueueFactory::MOCK_HANDLER_QUEUE_COOKIE,
            $value
        );
    }

    #[Given('tracking cookies are set')]
    public function trackingCookiesAreSet(TableNode $table): void
    {
        foreach ($table->getRowsHash() as $key => $value) {
            $this->getSession()->setCookie($key, $value);
        }
    }

}
