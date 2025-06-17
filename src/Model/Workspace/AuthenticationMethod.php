<?php

namespace Synerise\SyliusIntegrationPlugin\Model\Workspace;

use Synerise\Sdk\Model\AuthenticationMethodInterface;
use Synerise\Sdk\Model\EnumTrait;

enum AuthenticationMethod: string implements AuthenticationMethodInterface
{
    use EnumTrait;

    case Bearer = self::BEARER_VALUE;

    case Basic = self::BASIC_VALUE;
}
