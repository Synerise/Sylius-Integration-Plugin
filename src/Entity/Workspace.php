<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

class Workspace implements WorkspaceInterface
{

    private $id;

    public function getId(): string
    {
        return $this->id();
    }

    public function id(): string
    {
        return $this->id;
    }

}
