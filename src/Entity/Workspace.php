<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Model\Workspace\AuthenticationMethod;
use Synerise\SyliusIntegrationPlugin\Model\Workspace\Environment;
use Synerise\SyliusIntegrationPlugin\Model\Workspace\Mode;
use Synerise\SyliusIntegrationPlugin\Model\Workspace\PermissionsStatus;

class Workspace implements WorkspaceInterface, Config
{
    private ?int $id = null;

    private ?string $name = null;

    #[Assert\Uuid]
    private ?string $apiKey = null;

    /**
     * @Assert\Regex(
     *     pattern="/^[{(]?[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}[)}]?$/",
     *     message="This value is not a valid GUID."
     * )
     */
    private ?string $apiGuid = null;

    private AuthenticationMethod $authenticationMethod = AuthenticationMethod::Bearer;

    private Environment $environment = Environment::Azure;

    private PermissionsStatus $permissionsStatus = PermissionsStatus::NoAccess;

    private Mode $mode = Mode::Live;

    private bool $keepAliveEnabled = true;

    private float $liveTimeout = 2.5;

    private float $scheduledTimeout = 10;

    private bool $requestLoggingEnabled = false;

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getGuid(): ?string
    {
        return $this->apiGuid;
    }

    public function setGuid(?string $apiGuid): void
    {
        $this->apiGuid = $apiGuid;
    }

    public function getAuthenticationMethod(): AuthenticationMethod
    {
        return $this->authenticationMethod;
    }

    public function setAuthenticationMethod(AuthenticationMethod $authenticationMethod): void
    {
        $this->authenticationMethod = $authenticationMethod;
    }

    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    public function setEnvironment(Environment $environment): void
    {
        $this->environment = $environment;
    }

    public function getApiHost(): string
    {
        return $this->getEnvironment()->getApiHost();
    }

    public function getUserAgent(): string
    {
        return 'Sylius';
    }

    public function getTimeout(): ?float
    {
        return $this->getMode() == Mode::Scheduled ? $this->getScheduledTimeout() : $this->getLiveTimeout();
    }

    public function isKeepAliveEnabled(): bool
    {
        return $this->keepAliveEnabled;
    }

    public function setKeepAliveEnabled(bool $keepAliveEnabled): void
    {
        $this->keepAliveEnabled = $keepAliveEnabled;
    }

    public function getPermissionsStatus(): PermissionsStatus
    {
        return $this->permissionsStatus;
    }

    public function setPermissionsStatus(PermissionsStatus $permissionsStatus): void
    {
        $this->permissionsStatus = $permissionsStatus;
    }

    public function getMode(): Mode
    {
        return $this->mode;
    }

    public function setMode(Mode $mode): void
    {
        $this->mode = $mode;
    }

    public function getLiveTimeout(): float
    {
        return $this->liveTimeout;
    }

    public function setLiveTimeout(float $liveTimeout): void
    {
        $this->liveTimeout = $liveTimeout;
    }

    public function getScheduledTimeout(): float
    {
        return $this->scheduledTimeout;
    }

    public function setScheduledTimeout(float $scheduledTimeout): void
    {
        $this->scheduledTimeout = $scheduledTimeout;
    }

    public function isRequestLoggingEnabled(): bool
    {
        return $this->requestLoggingEnabled;
    }

    public function setRequestLoggingEnabled(bool $requestLoggingEnabled): void
    {
        $this->requestLoggingEnabled = $requestLoggingEnabled;
    }
}
