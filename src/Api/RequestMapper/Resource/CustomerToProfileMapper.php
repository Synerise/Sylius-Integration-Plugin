<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Resource;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Model\CustomerInterface as BaseCustomerInterface;
use Sylius\Resource\Model\ResourceInterface;
use Synerise\Api\V4\Models\Agreements;
use Synerise\Api\V4\Models\Attributes;
use Synerise\Api\V4\Models\Profile;
use Synerise\Api\V4\Models\ProfileSex;
use Webmozart\Assert\Assert;

class CustomerToProfileMapper implements RequestMapperInterface
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
     * @param CustomerInterface $resource
     */
    public function prepare(
        ResourceInterface $resource,
        string $type = 'synchronization',
        ?ChannelInterface $channel = null
    ): Profile
    {
        Assert::implementsInterface($resource, CustomerInterface::class);

        $profile = new Profile();
        $profile->setCustomId((string) $resource->getId());
        $profile->setEmail($resource->getEmail());
        $profile->setPhone($resource->getPhoneNumber());
        $profile->setFirstName($resource->getFirstName());
        $profile->setLastName($resource->getLastName());
        $profile->setBirthDate($resource->getBirthday()?->format("Y-m-d"));
        $profile->setSex($this->getClientGender($resource));

        $resourceDefaultAddress = $resource->getDefaultAddress();
        $profile->setCountryCode($resourceDefaultAddress?->getCountryCode());
        $profile->setProvince($resourceDefaultAddress?->getProvinceName());
        $profile->setZipCode($resourceDefaultAddress?->getPostcode());
        $profile->setCity($resourceDefaultAddress?->getCity());
        $profile->setAddress($resourceDefaultAddress?->getStreet());

        $additionalData = [
            "createdAt" => $resource->getCreatedAt()?->format(\DateTimeInterface::ATOM),
        ];

        if ($resource->getGroup() != null) {
            $additionalData['group'] = $resource->getGroup()->getCode();
        }

        $attributes = new Attributes();
        $attributes->setAdditionalData($additionalData);
        $profile->setAttributes($attributes);

        $agreements = new Agreements();
        $agreements->setEmail($resource->isSubscribedToNewsletter());
        $profile->setAgreements($agreements);

        return $profile;
    }

    private function getClientGender(CustomerInterface $resource): ProfileSex
    {
        $gender = self::$genderMap[$resource->getGender()] ?? ProfileSex::N_O_T__S_P_E_C_I_F_I_E_D;
        return new ProfileSex($gender);
    }
}

