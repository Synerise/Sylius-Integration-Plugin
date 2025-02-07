<?php

namespace Synerise\SyliusIntegrationPlugin\Ui\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function __invoke(MenuBuilderEvent $event): void
    {
        $syneriseMenu = $event->getMenu()
            ->addChild('synerise')
            ->setLabel('Synerise')
            ->setLabelAttribute('icon', 'tabler:currency-dollar');

        $syneriseMenu
            ->addChild('workspaces', ['route' => 'synerise_integration_admin_workspace_index'])
            ->setLabel('synerise_integration.ui.workspaces')
            ->setLabelAttribute('icon', 'file')
        ;

        $menuChildren = $event->getMenu()->getChildren();
        $marketingKey = array_search('marketing', array_keys($menuChildren)) ?: 4;
        ++$marketingKey;
        $menuChildren = array_slice($menuChildren, 0, $marketingKey, true) +
            ['synerise' => $menuChildren['synerise']] +
            array_slice($menuChildren, $marketingKey, (count($menuChildren) - 1) - $marketingKey, true);
        $event->getMenu()->setChildren($menuChildren);
    }
}
