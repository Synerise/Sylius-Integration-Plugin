<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Processor;

use DateTimeInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Model\CustomerInterface as BaseCustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Synerise\Api\V4\Clients\ClientsPostRequestBody;
use Synerise\Api\V4\Models\Agreements;
use Synerise\Api\V4\Models\Attributes;
use Synerise\Api\V4\Models\InBodyClientSex;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\SyliusIntegrationPlugin\Event\BeforeCustomerRequestEvent;
use Synerise\SyliusIntegrationPlugin\Service\EventService;

class CustomerUpdateProcessor implements CustomerProcessorInterface
{
    public function __construct(
        private ChannelContextInterface  $channel,
        private EventService             $eventService,
        private EventDispatcherInterface $eventDispatcher
    )
    {
    }

    protected static array $genderMap = [
        BaseCustomerInterface::MALE_GENDER => InBodyClientSex::M_A_L_E,
        BaseCustomerInterface::FEMALE_GENDER => InBodyClientSex::F_E_M_A_L_E,
        BaseCustomerInterface::UNKNOWN_GENDER => InBodyClientSex::N_O_T__S_P_E_C_I_F_I_E_D
    ];

    /**
     * @throws NotFoundException
     * @throws ExceptionInterface
     */
    public function process(CustomerInterface $customer): void
    {
        $channelId = $this->channel->getChannel()->getId();
        $clientRequestBody = $this->prepareCustomerRequestBody($customer);
        $this->eventService->processEvent("profile.update", $clientRequestBody, (string)$channelId);
    }

    protected function prepareCustomerRequestBody(CustomerInterface $customer): ClientsPostRequestBody
    {
        $client = new ClientsPostRequestBody();
        $client->setCustomId((string)$customer->getId());
        $client->setEmail($customer->getEmail());
        $client->setPhone($customer->getPhoneNumber());
        $client->setFirstName($customer->getFirstName());
        $client->setLastName($customer->getLastName());
        $client->setBirthDate($customer->getBirthday()?->format("Y-m-d"));
        $client->setSex($this->getClientGender($customer));

        $customerDefaultAddress = $customer->getDefaultAddress();
        $client->setCountryCode($customerDefaultAddress?->getCountryCode());
        $client->setProvince($customerDefaultAddress?->getProvinceName());
        $client->setZipCode($customerDefaultAddress?->getPostcode());
        $client->setCity($customerDefaultAddress?->getCity());
        $client->setAddress($customerDefaultAddress?->getStreet());

        $attributes = new Attributes();
        $attributes->setAdditionalData([
            "createdAt" => $customer->getCreatedAt()?->format(DateTimeInterface::ATOM),
        ]);
        $client->setAttributes($attributes);

        $agreements = new Agreements();
        $agreements->setEmail($customer->isSubscribedToNewsletter());
        $client->setAgreements($agreements);

        $event = new BeforeCustomerRequestEvent($client, $customer);
        $this->eventDispatcher->dispatch($event, BeforeCustomerRequestEvent::NAME);

        return $event->getClient();
    }

    protected function getClientGender(CustomerInterface $customer): InBodyClientSex
    {
        $gender = self::$genderMap[$customer->getGender()] ?? InBodyClientSex::N_O_T__S_P_E_C_I_F_I_E_D;
        return new InBodyClientSex($gender);
    }
}
