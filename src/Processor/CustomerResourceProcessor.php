<?php

namespace Synerise\SyliusIntegrationPlugin\Processor;

use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Model\CustomerInterface as BaseCustomerInterface;
use Sylius\Resource\Model\ResourceInterface;
use Synerise\Api\V4\Models\Agreements;
use Synerise\Api\V4\Models\Attributes;
use Synerise\Api\V4\Models\Profile;
use Synerise\Api\V4\Models\ProfileSex;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationDataType;
use Webmozart\Assert\Assert;

class CustomerResourceProcessor implements ResourceProcessorInterface
{
    /**
     * @var array
     */
    protected static array $genderMap = [
        BaseCustomerInterface::MALE_GENDER => ProfileSex::M_A_L_E,
        BaseCustomerInterface::FEMALE_GENDER => ProfileSex::F_E_M_A_L_E,
        BaseCustomerInterface::UNKNOWN_GENDER => ProfileSex::N_O_T__S_P_E_C_I_F_I_E_D
    ];

    /**
     * @param string $resourceType
     * @return bool
     */
    public function supports(string $resourceType): bool
    {
        return $resourceType == SynchronizationDataType::Customer;
    }

    /**
     * @param CustomerInterface $resource
     * @return Profile
     */
    public function process(ResourceInterface $resource): Profile
    {
        Assert::implementsInterface($resource, CustomerInterface::class);

        return $this->prepareProfileRequestBody($resource);
    }

    /**
     * @param CustomerInterface $customer
     * @return Profile
     */
    private function prepareProfileRequestBody(CustomerInterface $customer): Parsable
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

        return $profile;
    }

    public function getClientGender(CustomerInterface $customer): ProfileSex
    {
        $gender = self::$genderMap[$customer->getGender()] ?? ProfileSex::N_O_T__S_P_E_C_I_F_I_E_D;
        return new ProfileSex($gender);
    }
}

