<?php

namespace Synerise\SyliusIntegrationPlugin\Model\Workspace;


use Synerise\Sdk\Model\EnumTrait;
use Synerise\Sdk\Model\EnvironmentInterface;
use Synerise\Sdk\Model\EnvironmentTrait;

enum Environment: string implements EnvironmentInterface
{
    use EnvironmentTrait;
    use EnumTrait;

    case Azure = self::AZURE_VALUE;

    case GCP = self::GCP_VALUE;
}
