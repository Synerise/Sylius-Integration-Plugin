<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Command;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Synerise\SyliusIntegrationPlugin\Entity\Synchronization;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationDataType;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationStatus;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncStartMessage;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationConfigurationRepository;
use Webmozart\Assert\Assert;

class CreateSynchronizationCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'synerise:create-synchronization';

    /** @var array<string> */
    private const ALLOWED_RESOURCES = ['product', 'order', 'customer'];

    /** @var ChannelRepositoryInterface<ChannelInterface> */
    private ChannelRepositoryInterface $channelRepository;

    private MessageBusInterface $messageBus;

    private EntityManagerInterface $entityManager;

    private SynchronizationConfigurationRepository $synchronizationConfigurationRepository;

    /** @param ChannelRepositoryInterface<ChannelInterface> $channelRepository */
    public function __construct(
        EntityManagerInterface $entityManager,
        ChannelRepositoryInterface $channelRepository,
        MessageBusInterface $messageBus,
        SynchronizationConfigurationRepository $synchronizationConfigurationRepository,
    ) {
        parent::__construct(self::$defaultName);
        $this->messageBus = $messageBus;
        $this->channelRepository = $channelRepository;
        $this->entityManager = $entityManager;
        $this->synchronizationConfigurationRepository = $synchronizationConfigurationRepository;
    }

    protected function configure(): void
    {
        $this->setDescription('Synchronizes data with synerise via API.')
            ->addOption(
                'resource',
                null,
                InputOption::VALUE_REQUIRED,
                'Resource type name Product, Customer, Order',
                null,
                self::ALLOWED_RESOURCES,
            )
            ->addOption('sales-channel', null, InputOption::VALUE_REQUIRED, 'Sales Channel');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $typeArg = $input->getOption('resource');
        $salesChannelArg = $input->getOption('sales-channel');

        Assert::string($typeArg, 'Missing required params.');
        Assert::string($salesChannelArg, 'Missing required params.');
        Assert::oneOf($typeArg, self::ALLOWED_RESOURCES, sprintf(
            'Invalid resource type "%s". Must be one of: %s',
            $typeArg,
            implode(', ', self::ALLOWED_RESOURCES),
        ));

        $salesChannel = $this->channelRepository->findOneByCode($salesChannelArg);
        if (!$salesChannel) {
            $output->writeln('<error>Sales Channel with code: ' . $salesChannelArg . ' not found</error>');

            return Command::FAILURE;
        }

        $configuration = $this->synchronizationConfigurationRepository->findOneByChannel($salesChannel);
        if (!$configuration) {
            $output->writeln('<error>Synchronization configuration for the Sales Channel ' . $salesChannel->getCode() . ' not found</error>');

            return Command::FAILURE;
        }

        $configurationSnapshot = json_encode($configuration);
        if (!$configurationSnapshot) {
            $output->writeln('<error>Failed to prepare configuration snapshot.</error>');

            return Command::FAILURE;
        }

        $synchronization = new Synchronization();
        $synchronization->setChannel($salesChannel);
        $synchronization->setType(SynchronizationDataType::from($typeArg));
        $synchronization->setStatus(SynchronizationStatus::Created);
        $synchronization->setConfigurationSnapshot($configurationSnapshot);
        $synchronization->setCreatedAt(new \DateTimeImmutable());
        $synchronization->setSent(0);

        $this->entityManager->persist($synchronization);
        $this->entityManager->flush();

        if (null === $synchronization->getId()) {
            $output->writeln('<error>Failed to persist synchronization.</error>');

            return Command::FAILURE;
        }

        $syncStartMessage = new SyncStartMessage($synchronization->getId(), $typeArg);
        $this->messageBus->dispatch($syncStartMessage);

        $output->writeln('<info>Created new synchronization ID: ' . $synchronization->getId() . '</info>');

        return Command::SUCCESS;
    }
}
