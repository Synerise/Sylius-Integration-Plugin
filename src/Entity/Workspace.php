<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

class Workspace implements WorkspaceInterface
{

    private ?int $id = null;

    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
