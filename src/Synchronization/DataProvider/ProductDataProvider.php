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

    public function getIds(ChannelInterface $channel, DateTimeImmutable $sinceWhen, DateTimeImmutable $untilWhen): iterable
    {
        // @phpstan-ignore-next-line
        $queryBuilder = $this->productRepository->createQueryBuilder('p');
        $queryBuilder->select('p.id')
        ->where(':channel MEMBER OF p.channels')
        ->andWhere('(p.createdAt BETWEEN :sinceWhen AND :untilWhen) OR (p.updatedAt BETWEEN :sinceWhen AND :untilWhen)')
        ->setParameters(new ArrayCollection([
            new Parameter('channel', $channel),
            new Parameter('sinceWhen', $sinceWhen),
            new Parameter('untilWhen', $untilWhen)
        ]));
        return $queryBuilder->getQuery()->toIterable();
    }

    public function getEntity(int $id): ?ResourceInterface
    {
        return $this->productRepository->find($id);
    }
}
