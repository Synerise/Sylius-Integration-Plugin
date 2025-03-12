<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;
use Synerise\SyliusIntegrationPlugin\Model\AuthenticationMethod;
use Synerise\SyliusIntegrationPlugin\Model\Environment;

interface WorkspaceInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(string $name): void;

    public function getApiKey(): ?string;

    public function setApiKey(string $apiKey): void;

    public function getGuid(): ?string;

    public function setGuid(string $apiGuid): void;

    public function getAuthenticationMethod(): ?AuthenticationMethod;

    public function setAuthenticationMethod(AuthenticationMethod $authenticationMethod): void;

    public function getEnvironment(): ?Environment;

    public function setEnvironment(Environment $environment): void;

    public function getApiHost(): ?string;

    public function getUserAgent(): string;

    public function getTimeout(): ?float;

    public function isKeepAliveEnabled(): bool;

    public function getPermissions(): ?array;

    public function setPermissions(?array $permissions): void;
}
