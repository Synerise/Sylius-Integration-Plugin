<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Services;

use Symfony\Component\HttpKernel\KernelInterface;

final class LogParser
{
    private string $logFile;

    private array $entries = [];

    public function __construct(private KernelInterface $kernel)
    {
        $this->logFile = $this->kernel->getLogDir() . '/synerise.log';
    }

    public function purgeLogs(): void
    {
        if (file_exists($this->logFile)) {
            file_put_contents($this->logFile, '');
        }
    }

    public function loadEntries(): void
    {
        if (!file_exists($this->logFile)) {
            return;
        }

        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $entries = [];

        $key = 0;

        foreach ($lines as $line) {
            if (!str_contains($line, 'synerise.DEBUG')) {
                continue;
            }

            // Extract message part (after first ": ")
            $parts = explode(': ', $line, 2);
            if (count($parts) < 2) {
                continue;
            }

            $message = trim($parts[1]);

            // Detect curl request
            if (str_starts_with($message, 'curl')) {
                preg_match("/--url '([^']+)'/", $message, $m);
                $url = $m[1] ?? null;

                preg_match("/-X ([A-Z]+)/", $message, $m);
                $method = $m[1] ?? 'GET';

                preg_match("/-d '(.+)'/", $message, $m);
                $body = $m[1] ?? null;

                $entries[$key] = [
                    'url' => $url,
                    'method' => $method,
                    'body' => $body,
                    'raw' => $message,
                ];
            }

            // Detect JSON response
            elseif (str_starts_with($message, '{')) {
                $message = preg_replace('/\s*\[\]\s*\[\]\s*$/', '', $message);
                $json = json_decode($message, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $entries[$key]['response'] = $json;
                }

                $key++;
            }
        }

        $this->entries = $entries;
    }

    public function getEntries(array $filters = []): array
    {
        if (empty($this->entries)) {
            $this->loadEntries();
        }

        $result = [];
        foreach ($this->entries as $entry) {
            if (!empty($filters)) {
                if (isset($filters['url'])) {
                    if (str_contains($entry['url'], $filters['url'])) {
                        $result[] = $entry;
                    }
                }
            } else {
                $result[] = $this->entries;
            }
        }

        return $result;
    }
}
