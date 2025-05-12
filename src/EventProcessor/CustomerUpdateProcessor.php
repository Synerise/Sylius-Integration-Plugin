<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\EventProcessor;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Model\CustomerInterface as BaseCustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Synerise\Api\V4\Models\Agreements;
use Synerise\Api\V4\Models\Attributes;
use Synerise\Api\V4\Models\Profile;
use Synerise\Api\V4\Models\ProfileSex;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\BeforeProfileRequestEvent;
use Synerise\SyliusIntegrationPlugin\EventHandler\EventHandlerFactory;
use Webmozart\Assert\Assert;

class CustomerUpdateProcessor implements CustomerProcessorInterface
{
    public function __construct(
        private ChannelConfigurationFactory $configurationFactory,
        private EventHandlerFactory $eventHandlerFactory,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    protected static array $genderMap = [
        BaseCustomerInterface::MALE_GENDER => ProfileSex::M_A_L_E,
        BaseCustomerInterface::FEMALE_GENDER => ProfileSex::F_E_M_A_L_E,
        BaseCustomerInterface::UNKNOWN_GENDER => ProfileSex::N_O_T__S_P_E_C_I_F_I_E_D
    ];

    public function process(CustomerInterface $customer): void
    {
        $configuration = $this->configurationFactory->get();
        if (!$type = $configuration?->getEventHandlerType("profile.update")) {
            return;
        }

        Assert::NotNull($configuration->getChannel());

        $this->eventHandlerFactory->getHandlerByType($type)->processEvent(
            "profile.update",
            $this->prepareCustomerRequestBody($customer),
            $configuration->getChannel()->getId()
        );
    }

    protected function prepareCustomerRequestBody(CustomerInterface $customer): Profile
    {
        $profile = new Profile();
        $profile->setCustomId((string)$customer->getId());
        $profile->setEmail($customer->getEmail());
        $profile->setPhone($customer->getPhoneNumber());
        $profile->setFirstName($customer->getFirstName());
        $profile->setLastName($customer->getLastName());
        $profile->setBirthDate($customer->getBirthday()?->format("Y-m-d"));
        $profile->setSex($this->getClientGender($customer));

        $customerDefaultAddress = $customer->getDefaultAddress();
        $profile->setCountryCode($customerDefaultAddress?->getCountryCode());
        $profile->setProvince($customerDefaultAddress?->getProvinceName());
        $profile->setZipCode($customerDefaultAddress?->getPostcode());
        $profile->setCity($customerDefaultAddress?->getCity());
        $profile->setAddress($customerDefaultAddress?->getStreet());

        $attributes = new Attributes();
        $attributes->setAdditionalData([
            "createdAt" => $customer->getCreatedAt()?->format(\DateTimeInterface::ATOM),
        ]);
        $profile->setAttributes($attributes);

        $agreements = new Agreements();
        $agreements->setEmail($customer->isSubscribedToNewsletter());
        $profile->setAgreements($agreements);

        $event = new BeforeProfileRequestEvent($profile, $customer);
        $this->eventDispatcher->dispatch($event, BeforeProfileRequestEvent::NAME);

        return $event->getProfile();
    }

    protected function getClientGender(CustomerInterface $customer): ProfileSex
    {
        $gender = self::$genderMap[$customer->getGender()] ?? ProfileSex::N_O_T__S_P_E_C_I_F_I_E_D;
        return new ProfileSex($gender);
    }
}
