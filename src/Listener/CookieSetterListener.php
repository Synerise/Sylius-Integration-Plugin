<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Synerise\SyliusIntegrationPlugin\Helper\CookieContainer;

class CookieSetterListener
{
    private CookieContainer $cookieContainer;

    public function __construct(CookieContainer $cookieContainer)
    {
        $this->cookieContainer = $cookieContainer;
    }

    public function setCookies(ResponseEvent $event): void
    {
        foreach ($this->cookieContainer->getCookies() as $cookie) {
            $event->getResponse()->headers->setCookie($cookie);
        }
    }
}
