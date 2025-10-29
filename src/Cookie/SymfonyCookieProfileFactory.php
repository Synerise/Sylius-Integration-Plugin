<?php

namespace Synerise\SyliusIntegrationPlugin\Cookie;

use Symfony\Component\HttpFoundation\RequestStack;
use Synerise\Sdk\Cookie\Constants;
use Synerise\Sdk\Cookie\CookieProfileFactory;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Model\Profile;
use Synerise\Sdk\Serialization\StringJsonParseNodeFactory;

class SymfonyCookieProfileFactory extends CookieProfileFactory
{
    private StringJsonParseNodeFactory $parseNodeFactory;

    public function __construct(
        private RequestStack $requestStack,
        ?StringJsonParseNodeFactory $parseNodeFactory = null
    ) {
        $this->parseNodeFactory = $parseNodeFactory ?: new StringJsonParseNodeFactory();

        parent::__construct($this->parseNodeFactory);
    }

    public function create(): Profile
    {
        $uuid = $this->getCookieValue(Constants::COOKIE_UUID);
        $p = $this->getCookieValue(Constants::COOKIE_P);
        if ($uuid == null || $p == null) {
            throw new NotFoundException('Tracking cookies unavailable');
        }

        $profile = new Profile();
        $profile->setUuid($uuid);
        $profile->setBaseParams($this->getBaseParams());
        $profile->setExtraParams($this->getExtraParams());

        return $profile;
    }

    protected function getBaseParams(): Profile\BaseParams
    {
        return $this->parseBaseParams($this->getCookieValue(Constants::COOKIE_P)) ?: new Profile\BaseParams();
    }

    protected function getExtraParams(): ?array
    {
        $params = $this->getCookieValue(Constants::COOKIE_PARAMS);
        return $params ? $this->parseNodeFactory->getRootParseNode($params)
            ->getCollectionOfPrimitiveValues('string') : null;
    }

    protected function parseBaseParams(?string $string): ?Profile\BaseParams
    {
        return $string ? $this->parseNodeFactory->getRootParseNode($string, 'key-value')
            ->getObjectValue([Profile\BaseParams::class, 'createFromDiscriminatorValue']) : null;
    }

    protected function getCookieValue(string $key): ?string
    {
        $value = $this->requestStack->getCurrentRequest()?->cookies->get($key);
        return $value ? (string) $value : null;
    }
}
