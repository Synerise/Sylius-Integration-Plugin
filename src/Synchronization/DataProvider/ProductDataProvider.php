<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Synchronization\DataProvider;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
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

    public function getIds(ChannelInterface $channel, ?\DateTimeImmutable $sinceWhen, ?\DateTimeImmutable $untilWhen): iterable
    {
        // @phpstan-ignore-next-line
        $queryBuilder = $this->productRepository->createQueryBuilder('p');
        $queryBuilder->select('p.id')
        ->where(':channel MEMBER OF p.channels')
        ->setParameter('channel', $channel);

        if ($sinceWhen !== null && $untilWhen !== null) {
            $queryBuilder
                ->andWhere('(p.createdAt BETWEEN :sinceWhen AND :untilWhen) OR (p.updatedAt BETWEEN :sinceWhen AND :untilWhen)')
                ->setParameter('sinceWhen', $sinceWhen)
                ->setParameter('untilWhen', $untilWhen);
        } elseif ($sinceWhen !== null) {
            $queryBuilder
                ->andWhere('p.createdAt >= :sinceWhen OR p.updatedAt >= :sinceWhen')
                ->setParameter('sinceWhen', $sinceWhen);
        } elseif ($untilWhen !== null) {
            $queryBuilder
                ->andWhere('p.updatedAt <= :untilWhen OR p.updatedAt <= :untilWhen')
                ->setParameter('untilWhen', $untilWhen);
        }

        return $queryBuilder->getQuery()->toIterable();
    }

    public function getEntity(int $id): ?ResourceInterface
    {
        return $this->productRepository->find($id);
    }
}
