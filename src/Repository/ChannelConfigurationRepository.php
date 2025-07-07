<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfiguration;

class ChannelConfigurationRepository extends EntityRepository
{
    private const ORDER_BY = ['id' => 'ASC'];

    public function findOneByChannel(ChannelInterface $channel): ?ChannelConfiguration
    {
        // @phpstan-ignore return.type
        return $this->findOneBy(['channel' => $channel], self::ORDER_BY);
    }
}
