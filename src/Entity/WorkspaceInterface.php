<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;
use Synerise\Sdk\Model\EnvironmentInterface;
use Synerise\Sdk\Model\AuthenticationMethodInterface;

interface WorkspaceInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(string $name): static;

    public function getApiKey(): ?string;

    public function setApiKey(string $apiKey): static;

    public function getGuid(): ?string;

    public function setGuid(string $apiGuid): static;

    public function getAuthenticationMethod(): ?AuthenticationMethodInterface;

    public function setAuthenticationMethod(?AuthenticationMethodInterface $authenticationMethod): static;

    public function getEnvironment(): ?EnvironmentInterface;

    public function setEnvironment(EnvironmentInterface $environment): static;

    public function getApiHost(): ?string;

    public function getUserAgent(): string;

    public function getTimeout(): ?float;

    public function isKeepAliveEnabled(): bool;

    public function getPermissions(): ?array;

    public function setPermissions(?array $permissions): static;
}
