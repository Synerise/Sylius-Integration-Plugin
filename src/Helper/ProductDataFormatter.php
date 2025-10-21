<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Helper;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface as CoreTaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

class ProductDataFormatter
{
    public const CATEGORY_PATH_DELIMITER = ' > ';

    public function __construct(
        private ChannelContextInterface $channelContext,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    private function prepareUrlContext(?ChannelInterface $channel = null): ChannelInterface
    {
        if ($channel === null) {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();
        }

        Assert::notNull($channel->getHostname());
        $this->urlGenerator->getContext()->setHost($channel->getHostname());

        return $channel;
    }

    public function generateProductUrl(ProductInterface $product, ?ChannelInterface $channel = null): string
    {
        $channel = $this->prepareUrlContext($channel);

        Assert::NotNull($channel->getDefaultLocale(), 'Default locale is not set');
        $localeCode = $channel->getDefaultLocale()->getCode();

        $parameters = [
                'slug' => $product->getTranslation($localeCode)->getSlug(),
                '_locale' => $localeCode,
        ];

        return $this->urlGenerator->generate(
            'sylius_shop_product_show',
            $parameters,
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
    }

    public function getMainImageUrl(ProductInterface $product, ?ChannelInterface $channel = null): ?string
    {
        $channel = $this->prepareUrlContext($channel);

        $imagePath = $this->getMainImage($product)?->getPath();

        return $this->urlGenerator->generate(
            'sylius_shop_default_locale',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL,
        ) . 'media/image/' . $imagePath;
    }

    public function formatAmount(float|int $amount): float
    {
        return abs($amount / 100);
    }

    /**
     * @param Collection<array-key, CoreTaxonInterface> $taxons
     *
     * @return string[]
     */
    public function formatTaxonsCollection(Collection $taxons): array
    {
        /** @var array<string> $filtered */
        $filtered = $taxons
            ->map(fn ($taxon) => $this->formatTaxon($taxon))
            ->filter(fn ($taxon) => $taxon !== null)
            ->toArray();

        return $filtered;
    }

    public function formatTaxon(?TaxonInterface $taxon): ?string
    {
        Assert::implementsInterface($taxon, TaxonInterface::class);
        return $taxon->getFullname(self::CATEGORY_PATH_DELIMITER);
    }

    private function getMainImage(ProductInterface $product): ?ImageInterface
    {
        return $product->getImagesByType('main')->first() ?: null;
    }
}
