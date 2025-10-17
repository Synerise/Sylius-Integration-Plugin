<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Synchronization\DataProvider;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;

class ProductDataProvider implements DataProviderInterface
{
    /**
     * @param ProductRepositoryInterface<ProductInterface> $productRepository
     */
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function getIds(ChannelInterface $channel): iterable
    {
        // @phpstan-ignore-next-line
        $queryBuilder = $this->productRepository->createQueryBuilder('o');
        $queryBuilder->select('o.id')
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $channel);

        return $queryBuilder->getQuery()->toIterable();
    }

    public function getEntity(int $id): ?ResourceInterface
    {
        return $this->productRepository->find($id);
    }
}
