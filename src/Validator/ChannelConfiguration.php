<?php

namespace Synerise\SyliusIntegrationPlugin\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ChannelConfiguration extends Constraint
{
    public string $cookieDomainDoesNotMatchHostname = 'Domain should be a substring of channel\'s hostname ({{ hostname }})';
    public string $channelHasNoHostname = 'Selected channel has no hostname defined';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
