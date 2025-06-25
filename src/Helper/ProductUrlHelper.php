<?php

namespace Synerise\SyliusIntegrationPlugin\Helper;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function PHPUnit\Framework\assertNotNull;

class ProductUrlHelper
{
    public function __construct(
        private ChannelContextInterface $channelContext,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function generate(ProductInterface $product, ?string $localeCode = null): string
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        AssertNotNull($channel->getDefaultLocale(), 'Default locale is not set');

        if ($localeCode === null) {
            $localeCode = $channel->getDefaultLocale()->getCode();
        }

        return $this->urlGenerator->generate(
            'sylius_shop_product_show',
            [
                'slug' =>  $product->getTranslation($localeCode)->getSlug(),
                '_locale' => $localeCode
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
