<?php

namespace Synerise\SyliusIntegrationPlugin\Helper;

use Symfony\Component\HttpFoundation\Cookie;
use Synerise\Sdk\Cookie\CookieAdapter;

class CookieContainer implements CookieAdapter
{
    /**
     * @var array<string,Cookie>
     */
    private array $cookies = [];

    public function setValue(string $name, string $value): void
    {
        $this->cookies[$name] = new Cookie(
            $name,
            $value,
            time() + 3600,
            '/',
            null,
            true,
            true
        );
    }

    /**
     * @return array<string,Cookie>
     */
    public function getCookies(): array {
        return $this->cookies;
    }
}
