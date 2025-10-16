<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Twig\Component;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationDataType;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationInterface;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationStatus;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationRepository;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationRepositoryInterface;

#[AsLiveComponent(
    template: '@SyneriseSyliusIntegrationPlugin/admin/synchronization_configuration/show/content/sections/synchronizations_list.html.twig',
)]
class SynchronizationsList
{
    use HookableLiveComponentTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public int $page = 1;

    #[LiveProp(writable: true)]
    public int $limit = 10;

    #[LiveProp(writable: true)]
    public ?string $sortBy = 'createdAt';

    #[LiveProp(writable: true)]
    public string $sortDirection = 'DESC';

    #[LiveProp(writable: true)]
    public ?string $filterStatus = null;

    #[LiveProp(writable: true)]
    public ?string $filterType = null;

    public string $template = '@SyneriseSyliusIntegrationPlugin/admin/synchronization_configuration/show/content/sections/synchronizations_list.html.twig';

    #[LiveProp(hydrateWith: 'hydrateChannel', dehydrateWith: 'dehydrateChannel')]
    #[ExposeInTemplate('channel')]
    public ChannelInterface $channel;

    /**
     * @param SynchronizationRepositoryInterface<SynchronizationInterface> $synchronizationRepository
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(
        private SynchronizationRepositoryInterface $synchronizationRepository,
        private ChannelRepositoryInterface $channelRepository,
    ) {
    }

    public function getSynchronizationDataTypeCases(): array
    {
        return SynchronizationDataType::cases();
    }

    public function getSynchronizationStatusCases(): array
    {
        return SynchronizationStatus::cases();
    }

    public function getSynchronizations(): ?array
    {
        /** @var array|null $synchronizations */
        $synchronizations = $this->createQueryBuilder()
            ->setMaxResults($this->limit)
            ->setFirstResult(($this->page - 1) * $this->limit)
            ->getQuery()
            ->getResult();

        return $synchronizations;
    }

    public function getTotal(): int
    {
        return $this->synchronizationRepository->countByChannelWithFilters(
            $this->channel,
            $this->getFilters(),
        );
    }

    public function getPages(): int
    {
        return (int) ceil($this->getTotal() / $this->limit);
    }

    private function createQueryBuilder(): QueryBuilder
    {
        // @phpstan-ignore-next-line
        $qb = $this->synchronizationRepository->createQueryBuilder('s')
            ->andWhere('s.channel = :channel')
            ->setParameter('channel', $this->channel);

        if ($this->filterStatus) {
            $qb->andWhere('s.status = :status')
                ->setParameter('status', SynchronizationStatus::from($this->filterStatus));
        }

        if ($this->filterType) {
            $qb->andWhere('s.type = :type')
                ->setParameter('type', SynchronizationDataType::from($this->filterType));
        }

        if ($this->sortBy) {
            $qb->orderBy('s.' . $this->sortBy, $this->sortDirection);
        }

        return $qb;
    }

    private function getFilters(): array
    {
        $filters = [];
        if ($this->filterStatus) {
            $filters['status'] = SynchronizationStatus::from($this->filterStatus);
        }
        if ($this->filterType) {
            $filters['type'] = SynchronizationDataType::from($this->filterType);
        }

        return $filters;
    }

    public function hydrateChannel(array $data): ?ChannelInterface
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneByCode($data['id']);

        return $channel;
    }

    public function dehydrateChannel(ChannelInterface $data): array
    {
        return [
            'id' => $data->getId(),
        ];
    }
}
