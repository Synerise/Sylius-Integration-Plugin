<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Resource;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Model\CustomerInterface as BaseCustomerInterface;
use Sylius\Resource\Model\ResourceInterface;
use Synerise\Api\V4\Models\Agreements;
use Synerise\Api\V4\Models\Profile;
use Synerise\Api\V4\Models\ProfileSex;
use Synerise\Sdk\Api\RequestBody\Models\ProfileBuilder;

use Webmozart\Assert\Assert;

class CustomerToProfileMapper implements RequestMapperInterface
{
    protected static array $genderMap = [
        BaseCustomerInterface::MALE_GENDER => ProfileSex::M_A_L_E,
        BaseCustomerInterface::FEMALE_GENDER => ProfileSex::F_E_M_A_L_E,
        BaseCustomerInterface::UNKNOWN_GENDER => ProfileSex::N_O_T__S_P_E_C_I_F_I_E_D,
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

        $resourceDefaultAddress = $resource->getDefaultAddress();

        $profileBuilder = ProfileBuilder::initialize()
            ->setEmail($resource->getEmail())
            ->setPhone($resource->getPhoneNumber())
            ->setFirstName($resource->getFirstName())
            ->setLastName($resource->getLastName())
            ->setBirthDate($resource->getBirthday()?->format("Y-m-d"))
            ->setSex($this->getClientGender($resource))
            ->setCountryCode($resourceDefaultAddress?->getCountryCode())
            ->setProvince($resourceDefaultAddress?->getProvinceName())
            ->setZipCode($resourceDefaultAddress?->getPostcode())
            ->setCity($resourceDefaultAddress?->getCity())
            ->setAddress($resourceDefaultAddress?->getStreet())
            ->addAttribute('createdAt', $resource->getCreatedAt()?->format(\DateTimeInterface::ATOM));

        if ($resource->getGroup() != null) {
            $profileBuilder->addAttribute('group', $resource->getGroup()->getCode());
        }

        $agreements = new Agreements();
        $agreements->setEmail($resource->isSubscribedToNewsletter());
        $profileBuilder->setAgreements($agreements);

        return $profileBuilder->build();
    }

    private function getClientGender(CustomerInterface $resource): ProfileSex
    {
        $gender = self::$genderMap[$resource->getGender()] ?? ProfileSex::N_O_T__S_P_E_C_I_F_I_E_D;

        return new ProfileSex($gender);
    }
}
