<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface WorkspaceInterface extends ResourceInterface
{
    public function getId(): ?int;
}
