<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Uid\Uuid;
use Synerise\Sdk\Model\EnvironmentEnum;
use Synerise\Sdk\Model\AuthenticationMethodEnum;

interface WorkspaceInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(string $name): static;

    public function getApiKey(): ?Uuid;

    public function setApiKey(Uuid $apiKey): static;

    public function getGuid(): ?string;

    public function setGuid(string $apiGuid): static;

    public function getAuthenticationMethod(): ?AuthenticationMethodEnum;

    public function setAuthenticationMethod(?AuthenticationMethodEnum $authenticationMethod): static;

    public function getEnvironment(): ?EnvironmentEnum;

    public function setEnvironment(EnvironmentEnum $environment): static;

    public function getApiHost(): ?string;

    public function getUserAgent(): string;

    public function getTimeout(): ?float;

    public function isKeepAliveEnabled(): bool;

    public function getPermissions(): ?array;

    public function setPermissions(?array $permissions): static;
}
