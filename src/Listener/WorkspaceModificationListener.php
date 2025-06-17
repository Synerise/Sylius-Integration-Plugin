<?php

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Synerise\SyliusIntegrationPlugin\Api\ClientBuilderFactory;
use Synerise\SyliusIntegrationPlugin\Entity\Workspace;
use Synerise\SyliusIntegrationPlugin\Model\Workspace\PermissionsStatus;

class WorkspaceModificationListener
{
    const REQUIRED_PERMISSIONS = [
        "API_CLIENT_CREATE"                     => 'CLIENT',
        "API_BATCH_CLIENT_CREATE"               => "CLIENT",
        "API_BATCH_TRANSACTION_CREATE"          => "TRANSACTION",
        "API_TRANSACTION_CREATE"                => "TRANSACTION",
        "API_CUSTOM_EVENTS_CREATE"              => "EVENTS",
        "API_ADDED_TO_CART_EVENTS_CREATE"       => "EVENTS",
        "API_REMOVED_FROM_CART_EVENTS_CREATE"   => "EVENTS",
        "API_ADDED_TO_FAVORITES_EVENTS_CREATE"  => "EVENTS",
        "API_LOGGED_IN_EVENTS_CREATE"           => "EVENTS",
        "API_LOGGED_OUT_EVENTS_CREATE"          => "EVENTS",
        "API_REGISTERED_EVENTS_CREATE"          => "EVENTS",
        "CATALOGS_CATALOG_CREATE"               => "CATALOG",
        "CATALOGS_CATALOG_READ"                 => "CATALOG",
        "CATALOGS_ITEM_BATCH_CATALOG_CREATE"    => "CATALOG",
        "TRACKER_CREATE"                        => "TRACKER"
    ];

    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager,
        private ClientBuilderFactory $clientBuilderFactory,
        private LoggerInterface $syneriseLogger,
    ) {
    }

    public function preSave(ResourceControllerEvent $event): void
    {
        /** @var Workspace $workspace */
        $workspace = $event->getSubject();

        try {
            $missingPermissions = $this->getMissingPermissions($workspace);
            $workspace->setPermissionsStatus($this->determineStatus($missingPermissions));
        } catch (\Exception $e) {
            $this->syneriseLogger->debug($e);
            $event->stop('Permissions check request failed');
        }
    }

    public function preShow(ResourceControllerEvent $event): void
    {
        /** @var Workspace $workspace */
        $workspace = $event->getSubject();

        try {
            $missingPermissions = $this->getMissingPermissions($workspace);
            $status = $this->determineStatus($missingPermissions);
            if ($status != $workspace->getPermissionsStatus()) {
                $workspace->setPermissionsStatus($status);
                $this->entityManager->persist($workspace);
            }

            if (!empty($missingPermissions)) {
                $flashes = FlashBagProvider::getFlashBag($this->requestStack);
                $flashes->add('permissions', $missingPermissions);
            }
        } catch (\Exception $e) {
            $this->syneriseLogger->debug($e);
            $event->stop('Permissions check request failed');
        }
    }

    private function determineStatus(array $missingPermissions): PermissionsStatus
    {
        if (empty($missingPermissions)) {
            return PermissionsStatus::FullAccess;
        } elseif (count($missingPermissions) === count(self::REQUIRED_PERMISSIONS)) {
            return PermissionsStatus::NoAccess;
        } else {
            return PermissionsStatus::PartialAccess;
        }
    }

    private function getMissingPermissions(Workspace $workspace): array
    {
        $missingPermissions = [];

        $clientBuilder = $this->clientBuilderFactory->create($workspace);
        $response = $clientBuilder->uauth()->apiKey()->permissionCheck()
            ->post(array_keys(self::REQUIRED_PERMISSIONS))->wait();

        if ($response && $response->getBusinessProfileName()) {
            $workspace->setName($response->getBusinessProfileName());
            $permissions = $response->getPermissions() ?: [];
            foreach($permissions as $permission => $isSet) {
                if(!$isSet) {
                    $missingPermissions[self::REQUIRED_PERMISSIONS[$permission]][] = $permission;
                }
            }
        } else {
            throw new \RuntimeException('Permissions check request failed. Empty response');
        }

        return $missingPermissions;
    }
}
