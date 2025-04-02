<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Processor;

use DateTimeInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Model\CustomerInterface as BaseCustomerInterface;
use Synerise\Api\V4\Models\Attributes;
use Synerise\Api\V4\Models\CreateClientRequestBody;
use Synerise\Api\V4\Models\InBodyClientSex;
use Synerise\Sdk\Api\RequestBody\Events\LoggedInBuilder;
use Synerise\Sdk\Api\RequestBody\Events\RegisteredBuilder;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Service\EventService;

class CustomerProcessor implements CustomerProcessorInterface
{
    private ChannelContextInterface $channel;
    private IdentityManager $identityManager;
    private EventService $eventService;

    public function __construct(
        ChannelContextInterface $channel,
        IdentityManager         $identityManagerProvider,
        EventService            $eventService
    )
    {
        $this->channel = $channel;
        $this->identityManager = $identityManagerProvider;
        $this->eventService = $eventService;
    }

    private static $genderMap = [
        BaseCustomerInterface::MALE_GENDER => InBodyClientSex::M_A_L_E,
        BaseCustomerInterface::FEMALE_GENDER => InBodyClientSex::F_E_M_A_L_E,
        BaseCustomerInterface::UNKNOWN_GENDER => InBodyClientSex::N_O_T__S_P_E_C_I_F_I_E_D
    ];

    public function process(CustomerInterface $customer, string $action): void
    {
        $clientRequestBody = $this->prepareCustomerRequestBody($customer);

        switch ($action) {
            case RegisteredBuilder::ACTION:
                $clientRegisterRequestBody = new CreateClientRequestBody();
                break;
            case LoggedInBuilder::ACTION:
                break;
        }

    }

    private function prepareCustomerRegisteredRequestBody(CustomerInterface $customer)
    {
        $client = $this->identityManager->getClient();
        $client->setEmail($customer->getEmail());
        $client->setCustomId($customer->getId());

        return RegisteredBuilder::initialize($client)
            ->build();
    }

    private function prepareCustomerRequestBody(CustomerInterface $customer): CreateClientRequestBody
    {
        $client = new CreateClientRequestBody();
        $client->setCustomId($customer->getId());
        $client->setEmail($customer->getEmail());
        $client->setPhone($customer->getPhoneNumber());
        $client->setFirstName($customer->getFirstName());
        $client->setLastName($customer->getLastName());
        $client->setBirthDate($customer->getBirthday());

        $customerDefaultAddress = $customer->getDefaultAddress();
        if ($customerDefaultAddress) {
            $client->setCountryCode($customerDefaultAddress->getCountryCode());
            $client->setProvince($customerDefaultAddress->getProvinceName());
            $client->setZipCode($customerDefaultAddress->getPostcode());
            $client->setCity($customerDefaultAddress->getCity());
            $client->setAddress($customerDefaultAddress->getStreet());
            $client->setSex($this->getClientGender($customer));
        }

        $attributes = new Attributes();
        $attributes->setAdditionalData([
            "createdAt" => $customer->getCreatedAt()->format(DateTimeInterface::ATOM),
        ]);
        $client->setAttributes($attributes);


        return $client;
    }


    private function getClientGender(CustomerInterface $customer)
    {
        return self::$genderMap[$customer->getGender()] ?? InBodyClientSex::N_O_T__S_P_E_C_I_F_I_E_D;
    }
}
