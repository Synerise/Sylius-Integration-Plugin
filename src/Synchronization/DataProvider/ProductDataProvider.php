<?php

namespace Synerise\SyliusIntegrationPlugin\Synchronization\DataProvider;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Resource\Model\ResourceInterface;

class ProductDataProvider implements DataProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function getIds(ChannelInterface $channel): iterable
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('o.id')
            ->from(Product::class, 'o')
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $channel);

        return $queryBuilder->getQuery()->toIterable();
    }

    /**
     * @return Product|null
     */
    public function getEntity(int $id): ?ResourceInterface
    {
        return $this->entityManager->getRepository(Product::class)->find($id);
    }
}
