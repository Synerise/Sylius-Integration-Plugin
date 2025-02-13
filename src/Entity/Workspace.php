<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Synerise\Sdk\Model\EnvironmentEnum;
use Synerise\Sdk\Api\Config;
use Synerise\Sdk\Model\AuthenticationMethodEnum;

class Workspace implements WorkspaceInterface, Config
{
    private ?int $id = null;

    #[Assert\NotBlank]
    private ?string $name = null;

    private ?Uuid $apiKey = null;

    #[Assert\Uuid]
    private ?string $apiGuid = null;

    private ?AuthenticationMethodEnum $authenticationMethod = null;

    private ?EnvironmentEnum $environment = null;

    private ?array $permissions = null;

    private ?ChannelInterface $channel = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getApiKey(): ?Uuid
    {
        return $this->apiKey;
    }

    public function setApiKey(Uuid $apiKey): static
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function getGuid(): ?string
    {
        return $this->apiGuid;
    }

    public function setGuid(string $apiGuid): static
    {
        $this->apiGuid = $apiGuid;

        return $this;
    }

    public function getAuthenticationMethod(): ?AuthenticationMethodEnum
    {
        return $this->authenticationMethod;
    }

    public function setAuthenticationMethod(?AuthenticationMethodEnum $authenticationMethod): static
    {
        $this->authenticationMethod = $authenticationMethod;

        return $this;
    }

    public function getEnvironment(): ?EnvironmentEnum
    {
        return $this->environment;
    }

    public function setEnvironment(EnvironmentEnum $environment): static
    {
        return $this;
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

    public function setPermissions(?array $permissions): static
    {
        $this->permissions = $permissions;

        return $this;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): static
    {
        $this->channel = $channel;

        return $this;
    }
}
