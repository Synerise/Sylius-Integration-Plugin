<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Synchronization\DataProvider;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;

class ProductDataProvider implements DataProviderInterface
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function getIds(ChannelInterface $channel): iterable
    {
        $queryBuilder = $this->productRepository->createQueryBuilder('o');
        $queryBuilder->select('o.id')
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $channel);
        
        return $queryBuilder->getQuery()->toIterable();
    }

    /**
     * @return Product|null
     */
    public function getEntity(int $id): ?ResourceInterface
    {
        return $this->productRepository->find($id);
    }
}
