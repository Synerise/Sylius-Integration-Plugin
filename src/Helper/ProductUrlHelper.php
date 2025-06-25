<?php

namespace Synerise\SyliusIntegrationPlugin\Helper;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductUrlHelper
{
    public function __construct(
        private ChannelContextInterface $channelContext,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function generate(ProductInterface $product, ?string $localeCode = null)
    {
        if ($localeCode === null) {
            $localeCode = $this->channelContext->getChannel()->getDefaultLocale()->getCode();
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
