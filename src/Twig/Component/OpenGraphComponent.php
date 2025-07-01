<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Twig\Component;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;

class OpenGraphComponent
{
    use HookableComponentTrait;

    #[ExposeInTemplate]
    public ?ProductInterface $product = null;

    #[ExposeInTemplate]
    public ?float $price = null;

    #[ExposeInTemplate(name: 'image_path')]
    public ?string $imagePath = null;

    #[ExposeInTemplate(name: 'original_price')]
    public ?float $originalPrice = null;

    #[ExposeInTemplate(name: 'has_discount')]
    public bool $hasDiscount = false;

    public function __construct(
        protected readonly ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        protected readonly ChannelContextInterface $channelContext,
        protected readonly CurrencyContextInterface $currencyContext,
        protected readonly CurrencyConverterInterface $currencyConverter,
        protected readonly ChannelConfigurationInterface $channelConfiguration,
    ) {
    }

    #[ExposeInTemplate('is_enabled')]
    public function isEnabled(): bool
    {
        return $this->channelConfiguration->isOpengraphEnabled();
    }

    #[PostMount]
    public function postMount(): void
    {
        if ($this->isEnabled() && null !== $this->product) {
            /** @var ProductVariantInterface $variant */
            $variant = $this->product->getVariants()->first();

            $price = $this->convertPrice(
                $this->productVariantPricesCalculator
                    ->calculate($variant, ['channel' => $this->channelContext->getChannel()]),
            );

            $originalPrice = $this->convertPrice(
                $this->productVariantPricesCalculator
                    ->calculateOriginal($variant, ['channel' => $this->channelContext->getChannel()]),
            );

            $this->price = $this->formatPrice($price);
            $this->originalPrice = $this->formatPrice($originalPrice);
            $this->hasDiscount = $originalPrice > $price;
            $this->imagePath = $this->getMainImage()?->getPath();
        }
    }

    private function convertPrice(int $price): int
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $currency = $channel->getBaseCurrency()?->getCode();

        if (null === $currency) {
            return $price;
        }

        return $this->currencyConverter->convert(
            $price,
            $currency,
            $this->currencyContext->getCurrencyCode(),
        );
    }

    private function formatPrice(int $amount): float
    {
        return abs($amount / 100);
    }

    private function getMainImage(): ?ImageInterface
    {
        return $this->product?->getImagesByType('main')->first() ?: null;
    }
}
