<?php

namespace Synerise\SyliusIntegrationPlugin\Helper;

use Symfony\Component\HttpFoundation\Cookie;
use Synerise\Sdk\Cookie\CookieAdapter;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;

class CookieContainer implements CookieAdapter
{
    /**
     * @var array<string,Cookie>
     */
    private array $cookies = [];

    public function __construct(private ChannelConfigurationInterface $channelConfiguration)
    {}

    public function setValue(string $name, string $value): void
    {
        $this->cookies[$name] = new Cookie(
            $name,
            $value,
            time() + 3600,
            '/',
            '.'.$this->channelConfiguration->getCookieDomain(),
            true,
            false
        );
    }

    /**
     * @return array<string,Cookie>
     */
    public function getCookies(): array {
        return $this->cookies;
    }
}
