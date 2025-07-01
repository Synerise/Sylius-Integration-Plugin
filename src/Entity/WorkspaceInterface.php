<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;
use Synerise\SyliusIntegrationPlugin\Model\Workspace\AuthenticationMethod;
use Synerise\SyliusIntegrationPlugin\Model\Workspace\Environment;
use Synerise\SyliusIntegrationPlugin\Model\Workspace\Mode;
use Synerise\SyliusIntegrationPlugin\Model\Workspace\PermissionsStatus;

interface WorkspaceInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(string $name): void;

    public function getApiKey(): ?string;

    public function setApiKey(string $apiKey): void;

    public function getGuid(): ?string;

    public function setGuid(string $apiGuid): void;

    public function getAuthenticationMethod(): AuthenticationMethod;

    public function setAuthenticationMethod(AuthenticationMethod $authenticationMethod): void;

    public function getEnvironment(): Environment;

    public function setEnvironment(Environment $environment): void;

    public function getApiHost(): ?string;

    public function getUserAgent(): string;

    public function getTimeout(): ?float;

    public function isKeepAliveEnabled(): bool;

    public function setKeepAliveEnabled(bool $keepAliveEnabled): void;

    public function getPermissionsStatus(): PermissionsStatus;

    public function setPermissionsStatus(PermissionsStatus $permissionsStatus): void;

    public function getMode(): Mode;

    public function setMode(Mode $mode): void;

    public function getLiveTimeout(): float;

    public function setLiveTimeout(float $liveTimeout): void;

    public function getScheduledTimeout(): float;

    public function setScheduledTimeout(float $scheduledTimeout): void;

    public function isRequestLoggingEnabled(): bool;

    public function setRequestLoggingEnabled(bool $requestLoggingEnabled): void;
}
