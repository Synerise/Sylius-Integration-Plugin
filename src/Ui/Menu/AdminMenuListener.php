<?php

namespace Synerise\SyliusIntegrationPlugin\Ui\Menu;

use Knp\Menu\Util\MenuManipulator;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    private readonly MenuManipulator $menuManipulator;

    public function __construct(MenuManipulator $menuManipulator)
    {
        $this->menuManipulator = $menuManipulator;
    }

    public function __invoke(MenuBuilderEvent $event): void
    {
        $syneriseMenu = $event->getMenu()
            ->addChild('synerise')
            ->setLabel('Synerise')
            ->setLabelAttribute('icon', 'tabler:currency-dollar');

        $syneriseMenu
            ->addChild('workspaces', ['route' => 'synerise_integration_admin_workspace_index'])
            ->setLabel('synerise_integration.workspace.index.title')
            ->setLabelAttribute('icon', 'file');

        $syneriseMenu
            ->addChild('configurations', ['route' => 'synerise_integration_admin_channel_configuration_index'])
            ->setLabel('synerise_integration.ui.channel_configurations')
            ->setLabelAttribute('icon', 'file');

        $syneriseMenu
            ->addChild('synchronization', ['route' => 'synerise_integration_admin_synchronization_configuration_index'])
            ->setLabel('synerise_integration.synchronization_configuration.index.title')
            ->setLabelAttribute('icon', 'file');

        $position = array_search('marketing', array_keys($event->getMenu()->getChildren())) ?: 4;
        $this->menuManipulator->moveToPosition($syneriseMenu, ++$position);
    }
}
