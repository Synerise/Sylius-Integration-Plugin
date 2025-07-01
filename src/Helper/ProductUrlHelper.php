<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Helper;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

class ProductUrlHelper
{
    public function __construct(
        private ChannelContextInterface $channelContext,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function generate(ProductInterface $product, ?ChannelInterface $channel = null): string
    {
        if ($channel === null) {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();
        }

        Assert::NotNull($channel->getDefaultLocale(), 'Default locale is not set');

        $localeCode = $channel->getDefaultLocale()->getCode();

        Assert::notNull($channel->getHostname());
        $this->urlGenerator->getContext()->setHost($channel->getHostname());

        return $this->urlGenerator->generate(
            'sylius_shop_product_show',
            [
                'slug' => $product->getTranslation($localeCode)->getSlug(),
                '_locale' => $localeCode,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }
}
