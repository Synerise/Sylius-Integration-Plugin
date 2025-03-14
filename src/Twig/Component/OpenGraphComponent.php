<?php

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
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent]
class OpenGraphComponent
{
    use HookableComponentTrait;

    #[ExposeInTemplate]
    public ?ProductInterface $product = null;

    #[ExposeInTemplate]
    public ?float $price = null;

    #[ExposeInTemplate]
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
    ) {
    }

    #[PostMount]
    public function postMount(): void
    {
        if ($this->product) {
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
            if ($this->getMainImage()) {
                $this->imagePath = $this->getMainImage()->getPath();
            }
        }
    }

    private function convertPrice(int $price): int
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        $currency = $channel->getBaseCurrency()?->getCode();

        if (!$currency) {
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

    /**
     * @return ImageInterface|false
     */
    private function getMainImage(): ImageInterface|false
    {
        $images = $this->product?->getImagesByType('main');
        if ($images && $images->count()) {
            return $images->first();
        }

        return false;
    }
}
