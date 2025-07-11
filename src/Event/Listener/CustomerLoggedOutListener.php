<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Listener;

use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Synerise\SyliusIntegrationPlugin\Event\Processor\CustomerProcessorInterface;
use Webmozart\Assert\Assert;

final readonly class CustomerLoggedOutListener
{
    public function __construct(
        private LoggerInterface $syneriseLogger,
        private CustomerProcessorInterface $customerProcessor,
    ) {
    }

    public function __invoke(LogoutEvent $event): void
    {
        try {
            /** @var UsernamePasswordToken $token */
            $token = $event->getToken();
            Assert::isInstanceOf($token, UsernamePasswordToken::class);

            if (str_contains($token->getFirewallName(), 'admin')) {
                return;
            }

            /** @var ShopUserInterface $user */
            $user = $token->getUser();
            Assert::isInstanceOf($user, ShopUserInterface::class);

            /** @var CustomerInterface $customer */
            $customer = $user->getCustomer();
            Assert::notNull($customer);

            $this->customerProcessor->process($customer);
        } catch (\Throwable $e) {
            $this->syneriseLogger->error($e);
        }
    }
}
