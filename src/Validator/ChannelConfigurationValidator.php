<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;

class ChannelConfigurationValidator extends ConstraintValidator
{
    /**
     * @param ChannelConfigurationInterface $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof ChannelConfigurationInterface) {
            throw new UnexpectedValueException($value, ChannelConfigurationInterface::class);
        }

        if (!$constraint instanceof ChannelConfiguration) {
            throw new UnexpectedValueException($constraint, ChannelConfiguration::class);
        }

        if (!$value->getChannel() || !$value->isTrackingEnabled()) {
            return;
        }

        $hostname = $value->getChannel()->getHostname();
        $cookieDomain = $value->getCookieDomain();

        if (!$hostname) {
            $this->context
                ->buildViolation($constraint->channelHasNoHostname)
                ->atPath('trackingEnabled')
                ->addViolation();

            return;
        }

        if ($cookieDomain && !str_contains($hostname, $cookieDomain)) {
            $this->context
                ->buildViolation($constraint->cookieDomainDoesNotMatchHostname)
                ->setParameter('{{ hostname }}', $hostname)
                ->atPath('cookieDomain')
                ->addViolation();
        }
    }
}
