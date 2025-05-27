<?php

namespace Synerise\SyliusIntegrationPlugin\Command;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
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

class CreateSynchronizationCommand extends Command
{
    protected static string $defaultName = 'synerise:create-synchronization';

    private ChannelRepositoryInterface $channelRepository;
    private MessageBusInterface $messageBus;
    private EntityManagerInterface $entityManager;
    private SynchronizationConfigurationRepository $synchronizationConfigurationRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ChannelRepositoryInterface $channelRepository,
        MessageBusInterface         $messageBus,
        SynchronizationConfigurationRepository $synchronizationConfigurationRepository
    )
    {
        parent::__construct(CreateSynchronizationCommand::$defaultName);
        $this->messageBus = $messageBus;
        $this->channelRepository = $channelRepository;
        $this->entityManager = $entityManager;
        $this->synchronizationConfigurationRepository = $synchronizationConfigurationRepository;
    }

    protected function configure(): void
    {
        $this->setDescription('Synchronizes data with synerise via API.')
            ->addOption('resource', null, InputOption::VALUE_REQUIRED, 'Resource type name Product, Customer, Order', null, ["product", "order", "customer"])
            ->addOption('sales-channel', null, InputOption::VALUE_REQUIRED, 'Sales Channel');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $typeArg = $input->getOption('resource');
        $salesChannelArg = (string)$input->getOption('sales-channel');

        $salesChannel = $this->channelRepository->findOneByCode($salesChannelArg);
        if(!$salesChannel) {
            $output->writeln('<error>Sales Channel with code: '.$salesChannel.' not found</error>');
            return Command::FAILURE;
        }

        $configuration = $this->synchronizationConfigurationRepository->findOneByChannel($salesChannel);
        if(!$configuration) {
            $output->writeln('<error>Configuration for the Sales Channel '.$salesChannel->getCode().' not found</error>');
            return Command::FAILURE;
        }

        $configurationSnapshot = json_encode($configuration);

        $synchronization = new Synchronization();
        $synchronization->setChannel($salesChannel);
        $synchronization->setType(SynchronizationDataType::from($typeArg));
        $synchronization->setStatus(SynchronizationStatus::Created);
        $synchronization->setConfigurationSnapshot($configurationSnapshot);
        $synchronization->setCreatedAt(new \DateTimeImmutable());
        $synchronization->setSent(0);

        $this->entityManager->persist($synchronization);
        $this->entityManager->flush();


        $syncStartMessage = new SyncStartMessage($synchronization->getId(), $synchronization->getType()->value);
        $this->messageBus->dispatch($syncStartMessage);

        $output->writeln('<info>Created new synchronization ID: '.$synchronization->getId().'</info>');
        return Command::SUCCESS;
    }
}
