<?php

namespace Synerise\SyliusIntegrationPlugin\Event\Listener;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Synerise\SyliusIntegrationPlugin\Event\Processor\CustomerProcessorInterface;
use Webmozart\Assert\Assert;

final readonly class CustomerLoggedInListener
{
    public function __construct(
        private CustomerProcessorInterface $customerProcessor,
    )
    {
    }

    public function __invoke(LoginSuccessEvent $event): void
    {
        if(str_contains($event->getFirewallName(), "admin")){
            return;
        }

        /** @var ShopUserInterface $user */
        $user = $event->getUser();
        Assert::isInstanceOf($user, ShopUserInterface::class);

        /** @var CustomerInterface $customer */
        $customer = $user->getCustomer();
        Assert::notNull($customer);

        $this->customerProcessor->process($customer);
    }
}
