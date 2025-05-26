<?php

namespace Synerise\SyliusIntegrationPlugin\Synchronization;

use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncMessage;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncStartMessage;

interface SynchronizationProcessorInterface
{
    public function dispatchSynchronization(SyncStartMessage $message): void;
    public function processSynchronization(SyncMessage $message): void;
}
