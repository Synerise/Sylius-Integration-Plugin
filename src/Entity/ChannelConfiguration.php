<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;

#[\Synerise\SyliusIntegrationPlugin\Validator\ChannelConfiguration]
class ChannelConfiguration implements ChannelConfigurationInterface
{
    private ?int $id = null;

    private ?ChannelInterface $channel = null;

    private ?WorkspaceInterface $workspace = null;

    private bool $trackingEnabled = false;

    private ?string $trackingCode = null;

    private ?string $cookieDomain = null;

    private bool $customPageVisit = false;

    private bool $virtualPage = false;

    private bool $opengraphEnabled = false;

    private ?array $events = null;

    private ?array $queueEvents = null;

    private bool $snrsParamsEnabled = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function getWorkspace(): ?WorkspaceInterface
    {
        return $this->workspace;
    }

    public function setWorkspace(?WorkspaceInterface $workspace): void
    {
        $this->workspace = $workspace;
    }

    public function isTrackingEnabled(): bool
    {
        return $this->trackingEnabled;
    }

    public function setTrackingEnabled(bool $trackingEnabled): void
    {
        $this->trackingEnabled = $trackingEnabled;
    }

    public function getTrackingCode(): ?string
    {
        return $this->trackingCode;
    }

    public function setTrackingCode(?string $trackingCode): void
    {
        $this->trackingCode = $trackingCode;
    }

    public function getCookieDomain(): ?string
    {
        return $this->cookieDomain;
    }

    public function setCookieDomain(?string $cookieDomain): void
    {
        $this->cookieDomain = $cookieDomain;
    }

    public function isCustomPageVisit(): bool
    {
        return $this->customPageVisit;
    }

    public function setCustomPageVisit(bool $customPageVisit): void
    {
        $this->customPageVisit = $customPageVisit;
    }

    public function isVirtualPage(): bool
    {
        return $this->virtualPage;
    }

    public function setVirtualPage(bool $virtualPage): void
    {
        $this->virtualPage = $virtualPage;
    }

    public function isOpengraphEnabled(): bool
    {
        return $this->opengraphEnabled;
    }

    public function setOpengraphEnabled(bool $opengraphEnabled): void
    {
        $this->opengraphEnabled = $opengraphEnabled;
    }

    public function getEvents(): array
    {
        return (array) $this->events;
    }

    public function setEvents(array $events): void
    {
        $this->events = $events;
    }

    public function getQueueEvents(): array
    {
        return (array) $this->queueEvents;
    }

    public function setQueueEvents(?array $queueEvents): void
    {
        $this->queueEvents = $queueEvents;
    }

    public function getEventHandlerType(string $action): ?string
    {
        if (!in_array($action, $this->getEvents())) {
            return null;
        }

        return in_array($action, $this->getQueueEvents()) ? 'message_queue' : 'live';
    }

    public function isSnrsParamsEnabled(): bool
    {
        return $this->snrsParamsEnabled;
    }

    public function setSnrsParamsEnabled(bool $snrsParamsEnabled): void
    {
        $this->snrsParamsEnabled = $snrsParamsEnabled;
    }
}
