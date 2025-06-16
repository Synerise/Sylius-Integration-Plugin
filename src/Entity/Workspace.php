<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Model\AuthenticationMethod;
use Synerise\SyliusIntegrationPlugin\Model\Environment;

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

    private ?AuthenticationMethod $authenticationMethod = null;

    private ?Environment $environment = null;

    private ?array $permissions = null;

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

    public function getAuthenticationMethod(): ?AuthenticationMethod
    {
        return $this->authenticationMethod;
    }

    public function setAuthenticationMethod(AuthenticationMethod $authenticationMethod): void
    {
        $this->authenticationMethod = $authenticationMethod;
    }

    public function getEnvironment(): ?Environment
    {
        return $this->environment;
    }

    public function setEnvironment(Environment $environment): void
    {
        $this->environment = $environment;
    }

    public function getApiHost(): ?string
    {
        return $this->getEnvironment()?->getApiHost();
    }

    public function getUserAgent(): string
    {
        return 'Sylius';
    }

    public function getTimeout(): ?float
    {
        return 2.5;
    }

    public function isKeepAliveEnabled(): bool
    {
        return false;
    }

    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    public function setPermissions(?array $permissions): void
    {
        $this->permissions = $permissions;
    }
}
