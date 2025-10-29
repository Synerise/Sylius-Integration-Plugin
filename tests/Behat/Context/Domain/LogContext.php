<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Then;
use Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\LogParser;
use Webmozart\Assert\Assert;

final class LogContext implements Context
{
    public function __construct(private LogParser $logParser)
    {
    }

    #[Then('logs should show :count request(s) to :uri')]
    #[Then('logs should show :count request(s) in total')]
    public function logsShouldShowRequest(string $count, ?string $uri = null): void
    {
        $filters = [];
        if ($uri !== null) {
            $filters['url'] = $uri;
        }

        $entries = $this->logParser->getEntries($filters);
        Assert::count($entries, $count);
    }

    #[Then('logs should show :count request(s) to :uri with data:')]
    public function logsShouldShowRequestWithData(string $count, string $uri = null, TableNode $data): void
    {
        $filters = [];
        if ($uri !== null) {
            $filters['url'] = $uri;
        }

        $entries = $this->logParser->getEntries($filters);

        foreach($entries as $entry) {
            foreach($data->getRowsHash() as $key => $value) {
                if($key == 'status_code') {
                    Assert::eq($entry['response']['status_code'], $value);
                } else {
                    Assert::contains($entry[$key], $value);
                }
            }
        }

        Assert::count($entries, $count);
    }
}
